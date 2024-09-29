<?php

use App\Http\Controllers\ProfileController;
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
Route::get('/bunbougus', 'App\Http\Controllers\BunbouguController@index')->name('bunbougus.index');
Route::get('/bunbougus/create', 'App\Http\Controllers\BunbouguController@create')->name('bunbougu.create')->middleware('auth');
Route::post('/bunbougus/store/', 'App\Http\Controllers\BunbouguController@store')->name('bunbougu.store')->middleware('auth');
Route::get('/bunbougus/edit/{bunbougu}', 'App\Http\Controllers\BunbouguController@edit')->name('bunbougu.edit')->middleware('auth');
Route::put('/bunbougus/edit/{bunbougu}','App\Http\Controllers\BunbouguController@update')->name('bunbougu.update')->middleware('auth');
Route::get('/bunbougus/show/{bunbougu}', 'App\Http\Controllers\BunbouguController@show')->name('bunbougu.show');
Route::delete('/bunbougus/{bunbougu}','App\Http\Controllers\BunbouguController@destroy')->name('bunbougu.destroy')->middleware('auth');

Route::get('/worktimes', 'App\Http\Controllers\WorktimeController@index')->name('worktime.index');
Route::get('/worktimes/create', 'App\Http\Controllers\WorktimeController@create')->name('worktime.create')->middleware('auth');
Route::post('/worktimes/store/', 'App\Http\Controllers\WorktimeController@store')->name('worktime.store')->middleware('auth');
Route::get('/worktimes/edit/{worktime}', 'App\Http\Controllers\WorktimeController@edit')->name('worktime.edit')->middleware('auth');
Route::put('/worktimes/edit','App\Http\Controllers\WorktimeController@update')->name('worktime.update')->middleware('auth');
Route::get('/worktimes/show/{worktime}', 'App\Http\Controllers\WorktimeController@show')->name('worktime.show');
Route::delete('/worktimes/{worktime}','App\Http\Controllers\WorktimeController@destroy')->name('worktime.destroy')->middleware('auth');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/dakokus', 'App\Http\Controllers\DakokuController@index')->name('dakokus.index');
Route::get('/test/sample', 'App\Http\Controllers\TestController@postData')->name('sample.postData');
Route::post('/test/sample', 'App\Http\Controllers\TestController@postData')->name('sample.postData');

// Excelインポート
Route::post('/students_import','App\Http\Controllers\StudentsController@import')->name('import');
// Excelエクスポート
Route::post('/students_export','App\Http\Controllers\StudentsController@export')->name('export'); //追加

Route::get('/pdf', 'App\Http\Controllers\ExportController@pdf')->name('pdf');
