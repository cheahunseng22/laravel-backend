<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/auth/listuser',[AuthController::class, 'getUser']);
Route::delete('/auth/del/{id}', [AuthController::class, 'deleteUser']);
Route::post('/admin/login', [AuthController::class, 'adminLogin']);


Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

//product
Route::apiResource('products', ProductController::class);


