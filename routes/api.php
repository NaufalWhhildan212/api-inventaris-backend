<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

use App\Http\Controllers\Api\AuthController;
// (Pastikan use ProductController yang kemarin tetap ada)

// Rute untuk Publik (Tidak perlu tiket/login)
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Rute yang butuh tiket/login (Kita masukkan produk ke sini sementara biar aman)
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', App\Http\Controllers\Api\ProductController::class);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', App\Http\Controllers\Api\ProductController::class);
    
    // Rute pemesanan barang
    Route::post('checkout', [App\Http\Controllers\Api\OrderController::class, 'checkout']);
});