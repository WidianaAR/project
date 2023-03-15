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
        Route::get('user/add', 'App\Http\Controllers\UserController@add_user')->name('add_user');
        Route::post('user/add', 'App\Http\Controllers\UserController@add_user_action')->name('add_user_action');
        Route::get('user/change/{id_user}', 'App\Http\Controllers\UserController@change_user')->name('change_user');
        Route::post('user/change', 'App\Http\Controllers\UserController@change_user_action')->name('change_user_action');
        // User Filter
        Route::get('user/filter/pjm', 'App\Http\Controllers\UserController@user_pjm')->name('user_pjm');
        Route::get('user/filter/kajur', 'App\Http\Controllers\UserController@user_kajur')->name('user_kajur');
        Route::get('user/filter/koorprodi', 'App\Http\Controllers\UserController@user_koorprodi')->name('user_koorprodi');
        Route::get('user/filter/auditor', 'App\Http\Controllers\UserController@user_auditor')->name('user_auditor');

        // Kelola Evaluasi Diri
        Route::get('evaluasi/set_time', 'App\Http\Controllers\EDController@set_time')->name('ed_set_time');
        Route::post('evaluasi/set_time', 'App\Http\Controllers\EDController@set_time_action')->name('ed_set_time_action');

        // Kelola Ketercapaian Standar
        Route::get('standar/set_time', 'App\Http\Controllers\KSController@set_time')->name('ks_set_time');
        Route::post('standar/set_time', 'App\Http\Controllers\KSController@set_time_action')->name('ks_set_time_action');
    });

    Route::group(['middleware' => ['cek_login:2']], function () {
        Route::get('kajur', function () {return view('home');});
    });
    Route::group(['middleware' => ['cek_login:3']], function () {
        Route::get('koorprodi', function () {return view('home');});
        
        Route::get('evaluasi/delete/{id_evaluasi}', 'App\Http\Controllers\EDController@delete')->name('ed_delete');
        Route::post('evaluasi', 'App\Http\Controllers\EDController@add')->name('ed_import_action');

        Route::get('standar/delete/{id_standar}', 'App\Http\Controllers\KSController@delete')->name('ks_delete');
        Route::post('standar', 'App\Http\Controllers\KSController@add')->name('ks_import_action');
    });
    Route::group(['middleware' => ['cek_login:4']], function () {
        Route::get('auditor', function () {return view('home');});
    });

    // Evaluasi Diri
    Route::get('evaluasi', 'App\Http\Controllers\EDController@home')->name('ed_home');
    Route::get('evaluasi/set_time/{id}', 'App\Http\Controllers\EDController@set_time_action_end')->name('ed_set_time_action_end');
    Route::get('evaluasi/table/{id_evaluasi}', 'App\Http\Controllers\EDController@table')->name('ed_table');
    Route::get('evaluasi/filter/year/{year}', 'App\Http\Controllers\EDController@filter_year')->name('ed_filter_year');

    // Ketercapaian Standar
    Route::get('standar', 'App\Http\Controllers\KSController@home')->name('ks_home');
    Route::get('standar/set_time/{id}', 'App\Http\Controllers\KSController@set_time_action_end')->name('ks_set_time_action_end');
    Route::get('standar/table/{id_standar}', 'App\Http\Controllers\KSController@table')->name('ks_table');
    Route::get('standar/filter/year/{year}', 'App\Http\Controllers\KSController@filter_year')->name('ks_filter_year');
});