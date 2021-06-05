<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('warehouse/translation', 'Controller@translations');

Route::group(['middleware' => 'auth'], function () {
    // Auth users

    Route::group(['prefix' => 'warehouse'], function () {
        // Somebody
        Route::get('/home', 'HomeController@index')->name('home');
        Route::get('/user/auth', 'HomeController@authUser')->name('authUser');
        Route::get('/user/seller', 'UserController@sellers')->name('sellers');
    });

    Route::group([
        'prefix' => 'warehouse',
        'middleware' => 'role',
        'roles' => ['admin', 'warehouse']
    ], function () {
        // Warehouse
        Route::resource('courier', 'CourierController');
        Route::resource('customer', 'CustomerController');
        Route::resource('box', 'BoxController');

        Route::get('packing-list/report', 'PackingListController@report')->name('packing-list.report');
        Route::post('packing-list/report', 'PackingListController@reportProcess');
        Route::post('packing-list/{id}/email', 'PackingListController@sendEmail')->name('packing-list.email');
        Route::get('packing-list/{id}/pdf', 'PackingListController@pdf')->name('packing-list.pdf');
        Route::get('packing-list/{id}/excel', 'PackingListController@excel')->name('packing-list.excel');
        Route::resource('packing-list', 'PackingListController');

        Route::get('order/report', 'OrderController@report')->name('order.report');
        Route::post('order/report', 'OrderController@reportProcess');
        Route::post('order/packing-list', 'OrderController@packingListProcess')->name('order.packingList');
        Route::get('order/{order}/labels', 'OrderController@labels')->name('order.labels');
        Route::resource('order', 'OrderController');

        Route::get('user/config', 'UserController@config')->name('user.config');
        Route::put('user/config', 'UserController@updateConfig')->name('user.updateConfig');
    });

    Route::group([
        'prefix' => 'seller',
        'middleware' => 'role',
        'roles' => ['seller']
    ], function () {
        // Seller
        Route::get('customer', 'CustomerController@index')->name('seller.customer.index');
        Route::get('customer/{customer}/edit', 'CustomerController@edit')->name('seller.customer.edit');

        Route::get('order/report', 'OrderController@report')->name('seller.order.report');
        Route::post('order/report', 'OrderController@reportProcess');
        Route::get('order', 'OrderController@index')->name('seller.order.index');
        Route::get('order/{order}/edit', 'OrderController@edit')->name('seller.order.edit');

        Route::get('courier', 'CourierController@index')->name('seller.courier.index');
    });

    Route::group([
        'prefix' => 'admin',
        'middleware' => 'role',
        'roles' => ['admin']
    ], function () {
        // Admin
        Route::resource('user', 'UserController');
        Route::get('user/exists/{email}', 'UserController@userExists');
    });
});
