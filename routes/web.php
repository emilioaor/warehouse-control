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

    Route::resource('courier', 'CourierController');
    Route::resource('customer', 'CustomerController');
    Route::resource('box', 'BoxController');
    Route::resource('order', 'OrderController');
});

Route::group(['middleware' => ['auth', 'admin'], 'prefix' => 'admin'], function () {
    Route::resource('user', 'UserController');
    Route::get('user/exists/{email}', 'UserController@userExists');
});
