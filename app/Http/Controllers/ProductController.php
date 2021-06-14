<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductUpdateRequest;
use App\Http\Requests\ProductStoreRequest;
use App\Models\Product;
use App\Responses\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Exception;

class ProductController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(Response::data(Product::with('category')->get()->toArray()));
    }

    /**
     * @param  ProductStoreRequest  $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(ProductStoreRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $product = Product::create($request->validated())->toArray();

            DB::commit();

            return response()->json(Response::data($product));
        } catch (Exception $exception) {
            DB::rollback();

            return response()->json(Response::error($exception->getMessage()));
        }
    }

    /**
     * @param  ProductUpdateRequest  $request
     * @param  int  $productId
     * @return JsonResponse
     * @throws Exception
     */
    public function update(ProductUpdateRequest $request, int $productId): JsonResponse
    {
        DB::beginTransaction();

        try {
            Product::where('id', $productId)->update($request->validated());

            DB::commit();

            return response()->json(Response::success('Ürünün stok bilgisi güncellenmiştir.'));
        } catch (Exception $exception) {
            DB::rollback();

            return response()->json(Response::error($exception->getMessage()));
        }
    }

    /**
     * @param  int  $productId
     * @return JsonResponse
     */
    public function destroy(int $productId): JsonResponse
    {
        DB::beginTransaction();

        try {
            Product::findOrFail($productId)->delete();

            DB::commit();

            return response()->json(Response::success('Ürün silinmiştir.'));
        } catch (Exception $exception) {
            DB::rollback();

            return response()->json(Response::error($exception->getMessage()));
        }
    }
}
