<?php

use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\CartController;
use Illuminate\Http\Request;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('products', [ProductController::class, 'index']);
Route::post('product', [ProductController::class, 'store']);

Route::post('carts/{id}', [CartController::class, 'store']);
Route::put('carts/{id}', [CartController::class, 'update']);
Route::delete('carts/{id}', [CartController::class, 'destroy']);
Route::get('carts/{id}', [CartController::class, 'show']);
