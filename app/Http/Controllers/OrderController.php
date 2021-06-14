<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderItemStoreRequest;
use App\Http\Requests\OrderStoreRequest;
use App\Models\Category;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Orders;
use App\Models\Product;
use App\Responses\Response;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $orders = Orders::with('items')->get();

        return response()->json(Response::data($orders->toArray()));
    }

    /**
     * @param  int  $customerId
     * @return JsonResponse
     */
    public function getCustomerOrders(int $customerId): JsonResponse
    {
        $orders = Orders::where('customer_id', $customerId)
            ->with(['items', 'customer'])
            ->get();

        if ($orders->isEmpty()) {
            return response()->json(Response::error('Bu müşterinin siparişi bulunamamıştır.'));
        }

        return response()->json(Response::data($orders->toArray()));
    }

    /**
     * @param  OrderItemStoreRequest  $request
     * @param  int  $customerId
     * @return JsonResponse
     * @throws Exception
     */
    public function addOrderItem(OrderItemStoreRequest $request, int $customerId): JsonResponse
    {
        if (!$this->checkCustomer($customerId)) {
            return response()->json(Response::error('Bu kritere uygun müşteri bulunamamıştır.'));
        }

        $product = Product::findOrFail($request->validated()['product_id']);

        if ($product->stock <= 0) {
            return response()->json(Response::error('Bu ürünün stoğu yeterli değildir.'));
        }

        $stockDecrease = $product->stock - $request->validated()['quantity'];

        if ($stockDecrease < 0) {
            return response()->json(Response::error('Bu ürünün stoğu yeterli değildir en fazla '
                . $product->stock . ' Adet satın alabilirsiniz'));
        }

        $total = $product->price * $request->validated()['quantity'];

        $orderItemData = $request->validated();
        $orderItemData['unit_price'] = $product->price;
        $orderItemData['total'] = $total;

        $orderItems = OrderItem::where('order_id', $request->validated()['order_id'])->sum('total');
        $orderTotalPrice = $orderItems + $orderItemData['total'];
        $order = Orders::findOrFail($request->validated()['order_id']);

        DB::beginTransaction();

        try {
            $orderItem = OrderItem::create($orderItemData)->toArray();

            if ($orderItem) {
                $order->total = $orderTotalPrice;
                $order->save();

                $product->stock = $stockDecrease;
                $product->save();
            }

            DB::commit();

            return response()->json(Response::data($orderItem));
        } catch (Exception $exception) {
            DB::rollback();

            return response()->json(Response::error($exception->getMessage()));
        }
    }

    /**
     * @param  int  $orderId
     * @return JsonResponse
     */
    public function getDiscount(int $orderId): JsonResponse
    {
        $getCheapestItemPurchase = $this->calculatedBuyTwoGetPercentCheapestItem($orderId);
        $getSixOverPurchases = $this->calculatedBuySixOverGetOneFree($orderId);

        $orderItemsTotal = OrderItem::where('order_id', $orderId)->sum('total');

        $calculatedDiscountData = ['order_id' => $orderId, 'discounts' => []];

        if ($orderItemsTotal >= 1000) {
            $calculatedDiscountData['discounts'][] =
                [
                    'discount_reason' => Category::where('id', 3)->first()->name,
                    'discount_amount' => ($orderItemsTotal / 100) * 10,
                    'sub_total' => $orderItemsTotal - ($orderItemsTotal / 100) * 10,
                ];
        }

        if ($getCheapestItemPurchase) {
            $calculatedDiscountData['discounts'][] =
                [
                    'discount_reason' => Category::where('id', 1)->first()->name,
                    'discount_amount' => (optional($getCheapestItemPurchase)->min_price / 100) * 20,
                    'sub_total' => $getCheapestItemPurchase->order_total -
                        (optional($getCheapestItemPurchase)->min_price / 100) * 20,
                ];
        }

        if ($getSixOverPurchases) {
            foreach ($getSixOverPurchases as $getSixOverPurchase) {
                $calculatedDiscountData['discounts'][] =
                    [
                        'discount_reason' => Category::where('id', 2)->first()->name,
                        'discount_amount' => $getSixOverPurchase->unit_price,
                        'sub_total' => $getSixOverPurchase->total - $getSixOverPurchase->unit_price,
                    ];
            }
        }

        $totalDiscount = collect($calculatedDiscountData['discounts'])->sum('discount_amount');
        $subTotal = collect($calculatedDiscountData['discounts'])->sum('sub_total');

        $calculatedDiscountData['total_discount'] = $totalDiscount;
        $calculatedDiscountData['discounted_total'] = $subTotal;

        return response()->json(Response::data($calculatedDiscountData));
    }

    /**
     * @param  int  $orderId
     * @return JsonResponse
     */
    public function destroy(int $orderId): JsonResponse
    {
        try {
            if (Orders::findOrFail($orderId)->delete()) {
                return response()->json(Response::success());
            }
        } catch (ModelNotFoundException $exception) {
            return response()->json(Response::error($exception->getMessage()));
        }
    }

    /**
     * @param  int  $orderId
     * @param  int  $orderItemId
     * @return JsonResponse
     */
    public function deleteOrderItem(int $orderId, int $orderItemId): JsonResponse
    {
        try {
            if (OrderItem::where('order_id', $orderId)->findOrFail($orderItemId)->delete()) {
                return response()->json(Response::success());
            }
        } catch (ModelNotFoundException $exception) {
            return response()->json(Response::error($exception->getMessage()));
        }
    }

    /**
     * @param  OrderStoreRequest  $request
     * @return JsonResponse
     */
    public function store(OrderStoreRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            DB::commit();

            return response()->json(Response::data(Orders::create($request->validated())->toArray()));
        } catch (Exception $exception) {
            DB::rollback();

            return response()->json(Response::error($exception->getMessage()));
        }
    }

    /**
     * @param  int  $orderId
     * @return OrderItem|null
     */
    private function calculatedBuyTwoGetPercentCheapestItem(int $orderId): ?OrderItem
    {
        return OrderItem::join('products', 'products.id', 'order_items.product_id')
            ->join('categories', 'categories.id', 'products.category')
            ->select([
                DB::raw('SUM( order_items.quantity ) as count'),
                'products.category',
                DB::raw('MIN( order_items.unit_price) as min_price'),
                DB::raw('SUM( order_items.total) as order_total'),
            ])
            ->where('products.category', 1)
            ->where('order_items.order_id', $orderId)
            ->groupBy('products.category')
            ->first();
    }

    /**
     * @param int $orderId
     * @return Collection|null
     */
    private function calculatedBuySixOverGetOneFree(int $orderId): ?Collection
    {
        return OrderItem::join('products', 'products.id', 'order_items.product_id')
            ->where('products.category', 2)
            ->where('order_items.order_id', $orderId)
            ->where('order_items.quantity', '>=', 6)
            ->get();
    }

    /**
     * @param  int  $customerId
     * @return bool
     */
    private function checkCustomer(int $customerId): bool
    {
        $checkCustomer = Customer::where('id', $customerId)->first();

        if ($checkCustomer === null) {
            return false;
        }

        return true;
    }
}
