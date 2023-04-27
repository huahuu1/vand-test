<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Auth\AuthController;
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

Route::controller(AuthController::class)->group(function () {
    //User register
    Route::post('register', 'register');
    //User login
    Route::post('login', 'login');
});


Route::middleware('auth:sanctum')->group(function () {
    //User logout
    Route::get('logout', [AuthController::class, 'logout']);
    //Store routes
    Route::controller(StoreController::class)->group(function () {
        //Get all stores
        Route::get('store/{max_per_page?}', 'getStores');
        //Get store by Id
        Route::get('store/id/{id}', 'getStoreById');
        //Search product by name
        Route::get('search/store/{max_per_page?}', 'searchStoreByName');
        //Create store
        Route::post('create/store', 'createStore');
        //Edit store
        Route::put('edit/store/id/{id}', 'editStore');
        //Detele store
        Route::delete('delete/store/id/{id}', 'deleteStore');
    });
    //Product routes
    Route::controller(ProductController::class)->group(function () {
        //Get all products
        Route::get('product/{max_per_page?}', 'getProducts');
        //Get product by Id
        Route::get('product/id/{id}', 'getProductById');
        //Search product by name
        Route::get('search/product/{max_per_page?}', 'searchProductByName');
        //Create product
        Route::post('create/product', 'createProduct');
        //Edit product
        Route::put('edit/product/id/{id}', 'editProduct');
        //Detele product
        Route::delete('delete/product/id/{id}', 'deleteProduct');
    });
});

