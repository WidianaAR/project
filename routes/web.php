<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// Login dan Logout
Route::get('login', 'App\Http\Controllers\UserController@login')->name('login');
Route::post('login', 'App\Http\Controllers\UserController@login_action')->name('login_action');
Route::get('logout', 'App\Http\Controllers\UserController@logout')->name('logout');

Route::group(['middleware' => ['auth']], function () {
    Route::group(['middleware' => ['cek_login:1']], function () {
        Route::get('pjm', function () {return view('home');});
    });
    Route::group(['middleware' => ['cek_login:2']], function () {
        Route::get('kajur', function () {return view('home');});
    });
    Route::group(['middleware' => ['cek_login:3']], function () {
        Route::get('koorprodi', function () {return view('home');});
    });
    Route::group(['middleware' => ['cek_login:4']], function () {
        Route::get('auditor', function () {return view('home');});
    });
});