<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// get user with middleware sanctum
Route::get('/user', [App\Http\Controllers\Api\AuthController::class, 'getUser'])->middleware('auth:sanctum');

Route::post('/user/register', [App\Http\Controllers\Api\AuthController::class, 'userRegister']);

//restaurant register
Route::post('/restaurant/register', [App\Http\Controllers\Api\AuthController::class, 'restaurantRegister']);

//driver register
Route::post('/driver/register', [App\Http\Controllers\Api\AuthController::class, 'driverRegister']);

//login
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'userLogin']);

//logout
Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'userLogout'])->middleware('auth:sanctum');

//update latlong
Route::put('/update-latlong', [App\Http\Controllers\Api\AuthController::class, 'updateLatLong'])->middleware('auth:sanctum');

//get all restaurant
Route::get('/restaurant', [App\Http\Controllers\Api\AuthController::class, 'getRestaurant'])->middleware('auth:sanctum');

//apiresource product
Route::apiResource('/products', App\Http\Controllers\Api\ProductController::class)->middleware('auth:sanctum');

//order
Route::post('/order', [App\Http\Controllers\Api\OrderController::class, 'createOrder'])->middleware('auth:sanctum');


//get order by user id
Route::get('/order/user', [App\Http\Controllers\Api\OrderController::class, 'orderHistory'])->middleware('auth:sanctum');

//get order by restaurant id
Route::get('/order/restaurant', [App\Http\Controllers\Api\OrderController::class, 'getOrdersByStatus'])->middleware('auth:sanctum');

//get order by driver id
Route::get('/order/driver', [App\Http\Controllers\Api\OrderController::class, 'getOrdersByStatusDriver'])->middleware('auth:sanctum');

//update order status
Route::put('/order/restaurant/update-status/{id}', [App\Http\Controllers\Api\OrderController::class, 'updateOrderStatus'])->middleware('auth:sanctum');

//update order status driver
Route::put('/order/driver/update-status/{id}', [App\Http\Controllers\Api\OrderController::class, 'updateOrderStatusDriver'])->middleware('auth:sanctum');

//update purchase status
Route::put('/order/user/update-status/{id}', [App\Http\Controllers\Api\OrderController::class, 'updatePurchaseStatus'])->middleware('auth:sanctum');
