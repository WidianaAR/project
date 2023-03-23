<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// Login dan Logout
Route::get('login', 'App\Http\Controllers\UserController@login')->name('login');
Route::post('login', 'App\Http\Controllers\UserController@login_action')->name('login_action');
Route::get('logout', 'App\Http\Controllers\UserController@logout')->name('logout');

Route::group(['middleware' => 'auth'], function () {
    Route::group(
        ['middleware' => 'cek_login:1'],
        function () {
            Route::get('pjm', 'App\Http\Controllers\KSChartController@home');

            // Kelola Akun User
            Route::get('user', 'App\Http\Controllers\UserController@user')->name('user');
            Route::get('user/delete/{id_user}', 'App\Http\Controllers\UserController@delete_user')->name('delete_user');
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
        }
    );


    Route::group(
        ['middleware' => 'cek_login:2'],
        function () {
            Route::get('kajur', 'App\Http\Controllers\KSChartController@home');

            Route::get('evaluasi/add', 'App\Http\Controllers\EDController@add')->name('ed_import');
            Route::get('evaluasi/change/{id_evaluasi}', 'App\Http\Controllers\EDController@change')->name('ed_change');
            Route::get('evaluasi/filter/prodi/{prodi_id}', 'App\Http\Controllers\EDController@filter_prodi')->name('ed_filter_prodi');

            Route::get('standar/add', 'App\Http\Controllers\KSController@add')->name('ks_import');
            Route::get('standar/change/{id_standar}', 'App\Http\Controllers\KSController@change')->name('ks_change');
            Route::get('standar/filter/prodi/{prodi_id}', 'App\Http\Controllers\KSController@filter_prodi')->name('ks_filter_prodi');
        }
    );


    Route::group(
        ['middleware' => 'cek_login:3'],
        function () {
            Route::get('koorprodi', 'App\Http\Controllers\KSChartController@home');
        }
    );


    Route::group(
        ['middleware' => 'cek_login:4'],
        function () {
            Route::get('auditor', 'App\Http\Controllers\KSChartController@home');

            Route::get('evaluasi/confirm/{id_evaluasi}', 'App\Http\Controllers\EDController@confirm')->name('ed_confirm');
            Route::get('evaluasi/cancel_confirm/{id_evaluasi}', 'App\Http\Controllers\EDController@cancel_confirm')->name('ed_cancel_confirm');
            Route::post('evaluasi/feedback', 'App\Http\Controllers\EDController@feedback')->name('ed_feedback');

            Route::get('standar/confirm/{id_standar}', 'App\Http\Controllers\KSController@confirm')->name('ks_confirm');
            Route::get('standar/cancel_confirm/{id_standar}', 'App\Http\Controllers\KSController@cancel_confirm')->name('ks_cancel_confirm');
            Route::post('standar/feedback', 'App\Http\Controllers\KSController@feedback')->name('ks_feedback');
        }
    );


    Route::group(
        [['middleware' => 'cek_login:2'] || ['middleware' => 'cek_login:3']],
        function () {
            Route::get('evaluasi/delete/{id_evaluasi}', 'App\Http\Controllers\EDController@delete')->name('ed_delete');
            Route::post('evaluasi', 'App\Http\Controllers\EDController@add_action')->name('ed_import_action');
            Route::post('evaluasi/change', 'App\Http\Controllers\EDController@change_action')->name('ed_change_action');

            Route::get('standar/delete/{id_standar}', 'App\Http\Controllers\KSController@delete')->name('ks_delete');
            Route::post('standar', 'App\Http\Controllers\KSController@add_action')->name('ks_import_action');
            Route::post('standar/change', 'App\Http\Controllers\KSController@change_action')->name('ks_change_action');
        }
    );


    Route::group(
        [['middleware' => 'cek_login:1'] || ['middleware' => 'cek_login:4']],
        function () {
            Route::post('evaluasi/export', 'App\Http\Controllers\EDController@export_all')->name('ed_export_all');
            Route::post('evaluasi/export/file', 'App\Http\Controllers\EDController@export_file')->name('ed_export_file');
            Route::get('evaluasi/filter/prodi/{prodi_id}', 'App\Http\Controllers\EDController@filter_prodi')->name('ed_filter_prodi');
            Route::get('evaluasi/filter/jurusan/{jurusan_id}', 'App\Http\Controllers\EDController@filter_jurusan')->name('ed_filter_jurusan');

            Route::post('standar/export', 'App\Http\Controllers\KSController@export_all')->name('ks_export_all');
            Route::post('standar/export/file', 'App\Http\Controllers\KSController@export_file')->name('ks_export_file');
            Route::get('standar/filter/prodi/{prodi_id}', 'App\Http\Controllers\KSController@filter_prodi')->name('ks_filter_prodi');
            Route::get('standar/filter/jurusan/{jurusan_id}', 'App\Http\Controllers\KSController@filter_jurusan')->name('ks_filter_jurusan');
        }
    );


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


    // Statistik Evaluasi Diri
    Route::get('ed_chart', 'App\Http\Controllers\EDChartController@home')->name('ed_chart');
    Route::post('ed_chart', 'App\Http\Controllers\EDChartController@home')->name('ed_chart_post');


    // Statistik Ketercapaian Standar
    Route::get('ks_chart', 'App\Http\Controllers\KSChartController@home')->name('ks_chart');
    Route::post('ks_chart', 'App\Http\Controllers\KSChartController@home')->name('ks_chart_post');
});