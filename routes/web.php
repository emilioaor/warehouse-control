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

Route::group(['middleware' => 'auth', 'prefix' => 'warehouse'], function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/user/auth', 'HomeController@authUser')->name('authUser');

    Route::resource('courier', 'CourierController');
    Route::resource('customer', 'CustomerController');
    Route::resource('box', 'BoxController');


    Route::get('order/report', 'OrderController@report')->name('order.report');
    Route::post('order/report', 'OrderController@reportProcess');
    Route::get('order/packing-list', 'OrderController@packingList')->name('order.packingList');
    Route::post('order/packing-list', 'OrderController@packingListProcess');
    Route::get('order/{order}/labels', 'OrderController@labels')->name('order.labels');
    Route::resource('order', 'OrderController');

    Route::get('user/config', 'UserController@config')->name('user.config');
    Route::put('user/config', 'UserController@updateConfig')->name('user.updateConfig');
});

Route::group(['middleware' => ['auth', 'admin'], 'prefix' => 'admin'], function () {
    Route::resource('user', 'UserController');
    Route::get('user/exists/{email}', 'UserController@userExists');
});
