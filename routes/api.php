<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\RatingController;
use App\Models\Category;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::post('/signup', [UserController::class, 'store']);
    Route::post('/login', [UserController::class, 'login'])->name('login');
    Route::apiResource('/user', UserController::class)->except(['store']);

    // Product Index dan Show
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function() {
        Route::post('/logout', [UserController::class, 'logout']);

        // Product
        Route::apiResource('products', ProductController::class)->except(['index', 'show']);
        Route::put('/products/{id}/restore', [ProductController::class, 'restore']);
        Route::get('/products/deleted/trash', [ProductController::class, 'showSoftDeleted']);

        // Categories
        Route::put('/categories/{id}/restore', [CategoryController::class, 'restore']);
        Route::get('categories/deleted', [CategoryController::class, 'showSoftDeleted']);
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::apiResource('/category', CategoryController::class);

        // Brand
        Route::put('brand/{id}/restore', [BrandController::class, 'restore']);
        Route::apiResource('brand', BrandController::class);

        Route::get('/brands/deleted/trash', [BrandController::class, 'showSoftDeleted']);
        Route::apiResource('brands', BrandController::class)->except(['store', 'show', 'update', 'destroy']);

        Route::post('/order-details', [OrderDetailController::class, 'store']);

        // Favorite
        Route::apiResource('/favorites', FavoriteController::class);

        Route::apiResource('/carts', CartController::class);


        // Rating
        Route::apiResource('/ratings', RatingController::class);

        Route::post('/payment/notification', [OrderDetailController::class, 'handlePaymentNotification']);

    });

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

});


