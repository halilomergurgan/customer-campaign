<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerUpdateRequest;
use App\Http\Requests\CustomerStoreRequest;
use App\Models\Customer;
use App\Responses\Response;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(Response::data(Customer::all()->toArray()));
    }

    /**
     * @param  CustomerStoreRequest  $request
     * @return JsonResponse
     */
    public function store(CustomerStoreRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $customer = Customer::create($request->validated())->toArray();

            DB::commit();

            return response()->json(Response::data($customer));
        } catch (Exception $exception) {
            DB::rollback();

            return response()->json(Response::error($exception->getMessage()));
        }
    }

    /**
     * @param  CustomerUpdateRequest  $request
     * @param  int  $customerId
     * @return JsonResponse
     */
    public function update(CustomerUpdateRequest $request, int $customerId): JsonResponse
    {
        DB::beginTransaction();

        try {
            Customer::where('id', $customerId)->update($request->validated());

            DB::commit();

            return response()->json(Response::success('Müşteri bilgisi güncellenmiştir.'));
        } catch (Exception $exception) {
            DB::rollback();

            return response()->json(Response::error($exception->getMessage()));
        }
    }

    /**
     * @param  int  $customerId
     * @return JsonResponse
     */
    public function destroy(int $customerId): JsonResponse
    {
        DB::beginTransaction();

        try {
            Customer::findOrFail($customerId)->delete();

            DB::commit();

            return response()->json(Response::success('Müşteri silinmiştir.'));
        } catch (Exception $exception) {
            DB::rollback();

            return response()->json(Response::error($exception->getMessage()));
        }
    }
}
