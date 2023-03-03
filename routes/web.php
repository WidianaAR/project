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

        // Kelola Akun User
        Route::get('user', 'App\Http\Controllers\UserController@user')->name('user');
        Route::get('user/{id_user}', 'App\Http\Controllers\UserController@delete_user')->name('delete_user');
        Route::get('add_user', 'App\Http\Controllers\UserController@add_user')->name('add_user');
        Route::post('add_user', 'App\Http\Controllers\UserController@add_user_action')->name('add_user_action');
        Route::get('change_user/{id_user}', 'App\Http\Controllers\UserController@change_user')->name('change_user');
        Route::post('change_user', 'App\Http\Controllers\UserController@change_user_action')->name('change_user_action');
        // User Filter
        Route::get('user/filter/pjm', 'App\Http\Controllers\UserController@user_pjm')->name('user_pjm');
        Route::get('user/filter/kajur', 'App\Http\Controllers\UserController@user_kajur')->name('user_kajur');
        Route::get('user/filter/koorprodi', 'App\Http\Controllers\UserController@user_koorprodi')->name('user_koorprodi');
        Route::get('user/filter/auditor', 'App\Http\Controllers\UserController@user_auditor')->name('user_auditor');
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