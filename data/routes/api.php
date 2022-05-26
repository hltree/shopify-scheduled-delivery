<?php

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

Route::group(['prefix' => 'csv_template', 'as' => 'csv_template.'], function () {
   Route::post('save', 'App\Http\Controllers\CsvTemplateController@save')->name('save');

   Route::get('list', 'App\Http\Controllers\CsvTemplateController@getNameList')->name('list');
   Route::get('values/{key}', 'App\Http\Controllers\CsvTemplateController@getValues')->name('value');
});
