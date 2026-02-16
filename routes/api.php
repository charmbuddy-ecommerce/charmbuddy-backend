<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ShippingController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\PaymentController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// AuthController
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/users_index', [AuthController::class, 'index']);

// ProductsController
Route::get('/products_index', [ProductController::class, 'index']);
Route::post('/products_store', [ProductController::class, 'store']);

// CategoryController
Route::get('/categories_index', [CategoryController::class, 'index']);
Route::post('/categories_store', [CategoryController::class, 'store']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show']);
});

// ShippingController
Route::prefix('shipping')->group(function () {
    Route::get('/provinces', [ShippingController::class, 'provinces']);
    Route::get('/cities', [ShippingController::class, 'cities']);
    Route::post('/cost', [ShippingController::class, 'cost']);
});

// FaqController
Route::get('/faqs', [FaqController::class, 'index']);
Route::post('/faqs_store', [FaqController::class, 'store']);

// Blok Sanctum
// Yang di dalem blok ini hanya bisa dipake kalo user udah login
// Kalo mau testing di ThunderClient, masukin token nya di bagian Auth, Bearer
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ]);
    });

    // AddressController
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::get('/addresses/primary', [AddressController::class, 'primary_address']);

    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'show']);
        Route::post('/', [CartController::class, 'add']);
        Route::put('/{itemId}', [CartController::class, 'update']);
        Route::delete('/{itemId}', [CartController::class, 'destroy']);
    });

    // Proses Beli, CheckoutController
    Route::post('/checkout', [CheckoutController::class, 'store']);

    // Lihat riwayat order, OrderController
    Route::get('/orders', [CheckoutController::class, 'index']);
    Route::get('/orders/{orderId}', [CheckoutController::class, 'show']);

    // Bayar atau Upload Bukti, PaymentController
    // URL: /api/orders/1/pay
    Route::post('/orders/{id}/pay', [PaymentController::class, 'store']);
});
