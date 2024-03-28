<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BrandController;
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

    Route::middleware('auth:sanctum')->group(function() {
        Route::post('/logout', [UserController::class, 'logout']);

        Route::get('/brand/{id}', [BrandController::class, 'show']);
        Route::post('/brand', [BrandController::class, 'store']);
        Route::apiResource('brands', BrandController::class)->except(['store', 'show']);
    });

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

});


