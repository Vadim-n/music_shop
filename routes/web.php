<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['register' => false]);

// API admin
Route::group(['middleware' => ['auth', 'admin'], 'prefix' => 'api'], function () {
//    Товары
    Route::get('/products', 'API\ProductController@index')->name('api_products_index');
    Route::post('/product', 'API\ProductController@add')->name('api_product_add');
    Route::get('/products/order', 'API\ProductController@orderList')->name('api_product_order_list');
    Route::put('/products/order', 'API\ProductController@order')->name('api_product_order');
    Route::get('/product/{productId}', 'API\ProductController@get')->name('api_product_get');
    Route::post('/product/{productId}', 'API\ProductController@edit')->name('api_product_edit');
    Route::put('/product/{productId}/activate', 'API\ProductController@activate')->name('api_product_activate');
    Route::get('/categories/{categoryId}/products', 'API\ProductController@index')->name('api_products_category_list');
    Route::get('/categories/{categoryId}/products/order', 'API\ProductController@orderList')->name('api_products_category_order_list');
    Route::put('/categories/{categoryId}/products/order', 'API\ProductController@order')->name('api_products_category_order');

//    Категории
    Route::get('/categories', 'API\CategoryController@index')->name('api_categories_index');
    Route::post('/category', 'API\CategoryController@add')->name('api_category_add');
    Route::get('/categories/order', 'API\CategoryController@orderList')->name('api_category_order_list');
    Route::put('/categories/order', 'API\CategoryController@order')->name('api_category_order');
    Route::get('/category/{categoryId}', 'API\CategoryController@get')->name('api_category_get');
    Route::post('/category/{categoryId}', 'API\CategoryController@edit')->name('api_category_edit');
    Route::put('/category/{categoryId}/activate', 'API\CategoryController@activate')->name('api_category_activate');
    Route::get('/categories/list', 'API\CategoryController@list')->name('api_categories_list');
});

// API client
Route::group(['prefix' => 'api/client'], function () {
//    Категории
    Route::get('/categories', 'API\Client\CategoryController@index')->name('api_client_categories_index');
    Route::get('/category/{categoryAlias}', 'API\Client\CategoryController@get')->name('api_client_category_get');

//    Товары
    Route::get('/product/{productAlias}', 'API\Client\ProductController@get')->name('api_client_product_get');
});

Route::get('/document/{documentName}', 'FileController@download')->name('file_download');

// WEB
Route::group(['middleware' => ['auth', ], 'prefix' => 'admin'], function () {
//    Товары
    Route::get('/', 'ProductController@index')->name('products');
    Route::get('/products/add', 'ProductController@add')->name('product_add');
    Route::get('/products/order', 'ProductController@order')->name('product_order');
    Route::get('/products/{productId}', 'ProductController@edit')->name('product_edit');

//    Категории
    Route::get('/categories', 'CategoryController@index')->name('categories');
    Route::get('/categories/add', 'CategoryController@add')->name('category_add');
    Route::get('/categories/order', 'CategoryController@order')->name('category_order');
    Route::get('/categories/{categoryId}', 'CategoryController@edit')->name('category_edit');
    Route::get('/categories/{categoryId}/products', 'CategoryController@products')->name('category_products');
    Route::get('/categories/{categoryId}/products/order', 'CategoryController@productsOrder')->name('category_products_order');
});

// Client WEB
Route::get('/', 'FrontController@index')->name('main');
Route::get('/contacts', 'FrontController@contacts')->name('contacts');
Route::get('/about', 'FrontController@about')->name('about');
Route::get('/{categoryAlias}', 'FrontController@category')->name('category');
Route::get('/{categoryAlias}/{productAlias}', 'FrontController@product')->name('product');
