<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/bunbougus', 'App\Http\Controllers\BunbouguController@index')->name('bunbougus.index');

Route::get('/bunbougus/create', 'App\Http\Controllers\BunbouguController@create')->name('bunbougu.create');
Route::post('/bunbougus/store/', 'App\Http\Controllers\BunbouguController@store')->name('bunbougu.store');

Route::get('/bunbougus/edit/{bunbougu}', 'App\Http\Controllers\BunbouguController@edit')->name('bunbougu.edit');
Route::put('/bunbougus/edit/{bunbougu}','App\Http\Controllers\BunbouguController@update')->name('bunbougu.update');

Route::get('/bunbougus/show/{bunbougu}', 'App\Http\Controllers\BunbouguController@show')->name('bunbougu.show');

Route::delete('/bunbougus/{bunbougu}','App\Http\Controllers\BunbouguController@destroy')->name('bunbougu.destroy');



Route::get('/worktimes', 'App\Http\Controllers\WorktimeController@index')->name('worktime.index');

Route::get('/worktimes/create', 'App\Http\Controllers\WorktimeController@create')->name('worktime.create');
Route::post('/worktimes/store/', 'App\Http\Controllers\WorktimeController@store')->name('worktime.store');

Route::get('/worktimes/edit/{worktime}', 'App\Http\Controllers\WorktimeController@edit')->name('worktime.edit');
Route::put('/worktimes/edit','App\Http\Controllers\WorktimeController@update')->name('worktime.update');

Route::get('/worktimes/show/{worktime}', 'App\Http\Controllers\WorktimeController@show')->name('worktime.show');

Route::delete('/worktimes/{worktime}','App\Http\Controllers\WorktimeController@destroy')->name('worktime.destroy');

