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

Route::get('/', 'App\Http\Controllers\HomeController@index')->name('home');

Route::get('sendAuthorize', 'App\Http\Controllers\AuthorizeController@index')->name('sendAuthorize');
Route::get('authRedirect', 'App\Http\Controllers\AuthRedirectController@index')->name('authRedirect');

Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {
    Route::get('/', 'App\Http\Controllers\SettingController@index')->name('index');
    Route::get('{themeId}/edit', 'App\Http\Controllers\SettingController@edit')->name('edit');
    Route::put('{themeId}', 'App\Http\Controllers\SettingController@update')->name('update');
});

Route::group(['prefix' => 'csv', 'as' => 'csv.'], function () {
});
