<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'orders'], function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::get('/{customerId}', [OrderController::class, 'getCustomerOrders']);
    Route::delete('/{orderId}', [OrderController::class, 'destroy']);
    Route::post('/{customerId}/add-item', [OrderController::class, 'addOrderItem']);
    Route::post('/', [OrderController::class, 'store']);
    Route::delete('/{orderId}/delete-item/{orderItemId}', [OrderController::class, 'deleteOrderItem']);
    Route::get('/{orderId}/discount', [OrderController::class, 'getDiscount']);
});

Route::group(['prefix' => 'products'], function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
    Route::put('/{productId}', [ProductController::class, 'update']);
    Route::delete('/{productId}', [ProductController::class, 'destroy']);
});

Route::group(['prefix' => 'customers'], function () {
    Route::get('/', [CustomerController::class, 'index']);
    Route::post('/', [CustomerController::class, 'store']);
    Route::put('/{customerId}', [CustomerController::class, 'update']);
    Route::delete('/{customerId}', [CustomerController::class, 'destroy']);
});
