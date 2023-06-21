<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// Login dan Logout
Route::get('login', 'App\Http\Controllers\AuthenticationController@login')->name('login');
Route::post('login', 'App\Http\Controllers\AuthenticationController@login_action')->name('login_action');


// Lupa password
Route::get('lupa_password', 'App\Http\Controllers\AuthenticationController@forget_pass')->name('forget_password');
Route::post('lupa_password', 'App\Http\Controllers\AuthenticationController@forget_pass_action')->name('forget_password_action');
route::get('reset_password/{token}', 'App\Http\Controllers\AuthenticationController@reset_pass')->name('reset_password');
route::post('reset_password', 'App\Http\Controllers\AuthenticationController@reset_pass_action')->name('reset_password_action');


Route::group(['middleware' => 'auth'], function () {
    Route::group(
        ['middleware' => 'cek_login:1'],
        function () {
            Route::get('pjm', 'App\Http\Controllers\EDChartController@home');

            // Kelola Akun User
            Route::get('user', 'App\Http\Controllers\UserController@user')->name('user');
            Route::get('user/delete/{id_user}', 'App\Http\Controllers\UserController@delete_user')->name('delete_user');
            Route::get('user/add', 'App\Http\Controllers\UserController@add_user')->name('add_user');
            Route::post('user/add', 'App\Http\Controllers\UserController@add_user_action')->name('add_user_action');
            Route::get('user/change/{id_user}', 'App\Http\Controllers\UserController@change_user')->name('change_user');
            Route::post('user/change/{id_user}', 'App\Http\Controllers\UserController@change_user_action')->name('change_user_action');
            Route::get('user/filter/{role}', 'App\Http\Controllers\UserController@user_filter')->name('user_filter');

            // Kelola Evaluasi Diri
            Route::get('evaluasi/set_time', 'App\Http\Controllers\EDDeadlineController@set_time')->name('ed_set_time');
            Route::post('evaluasi/set_time', 'App\Http\Controllers\EDDeadlineController@set_time_action')->name('ed_set_time_action');

            // Kelola Ketercapaian Standar
            Route::get('standar/set_time', 'App\Http\Controllers\KSDeadlineController@set_time')->name('ks_set_time');
            Route::post('standar/set_time', 'App\Http\Controllers\KSDeadlineController@set_time_action')->name('ks_set_time_action');

            // Kelola Jurusan
            Route::resource('jurusans', 'App\Http\Controllers\JurusanController');

            // Kelola Prodi
            Route::resource('prodis', 'App\Http\Controllers\ProdiController');
            Route::get('prodis/filter/{jurusan}', 'App\Http\Controllers\ProdiController@prodi_filter')->name('prodis_filter');

            // Kelola Panduan
            Route::resource('panduans', 'App\Http\Controllers\PanduanController');

            // Riwayat Aktivitas
            Route::get('logs', 'App\Http\Controllers\LogController@index')->name('logs');

            // Pengumuman
            Route::post('pengumuman', 'App\Http\Controllers\PengumumanController@store')->name('add_pengumuman');
        }
    );


    Route::group(
        ['middleware' => 'cek_login:2'],
        function () {
            Route::get('kajur', 'App\Http\Controllers\EDChartController@home');

            Route::get('evaluasi/add', 'App\Http\Controllers\EDController@add')->name('ed_import');
            Route::get('evaluasi/change/{id_evaluasi}', 'App\Http\Controllers\EDController@change')->name('ed_change');

            Route::get('standar/add', 'App\Http\Controllers\KSController@add')->name('ks_import');
            Route::get('standar/change/{id_standar}', 'App\Http\Controllers\KSController@change')->name('ks_change');
        }
    );


    Route::group(
        ['middleware' => 'cek_login:3'],
        function () {
            Route::get('koorprodi', 'App\Http\Controllers\EDChartController@home');

            Route::get('evaluasi/filter/{tahun}', 'App\Http\Controllers\EDController@filter_year')->name('ed_filter_year');
            Route::get('standar/filter/{tahun}', 'App\Http\Controllers\KSController@filter_year')->name('ks_filter_year');
        }
    );


    Route::group(
        ['middleware' => 'cek_login:4'],
        function () {
            Route::get('auditor', 'App\Http\Controllers\EDChartController@home');

            // Tilik
            Route::get('tilik', 'App\Http\Controllers\TilikController@index')->name('tilik_home');
            Route::post('tilik/filter', 'App\Http\Controllers\TilikController@index')->name('tilik_filter');
            Route::post('tilik/change', 'App\Http\Controllers\TilikController@change_action')->name('tilik_change');
            Route::get('tilik/evaluasi/table/{id}', 'App\Http\Controllers\TilikController@ed_table')->name('tilik_ed_table');
            Route::get('tilik/standar/table/{id}', 'App\Http\Controllers\TilikController@ks_table')->name('tilik_ks_table');
            Route::post('tilik/evaluasi', 'App\Http\Controllers\TilikController@ed_table_save')->name('tilik_ed_table_save');
            Route::post('tilik/standar', 'App\Http\Controllers\TilikController@ks_table_save')->name('tilik_ks_table_save');


            // Pasca Audit
            Route::get('pasca', 'App\Http\Controllers\PascaAuditController@index')->name('pasca_home');
            Route::post('pasca/filter', 'App\Http\Controllers\PascaAuditController@index')->name('pasca_filter');
            Route::get('pasca/evaluasi/table/{id}', 'App\Http\Controllers\PascaAuditController@ed_table')->name('pasca_ed_table');
            Route::get('pasca/standar/table/{id}', 'App\Http\Controllers\PascaAuditController@ks_table')->name('pasca_ks_table');
            Route::post('pasca/evaluasi', 'App\Http\Controllers\PascaAuditController@ed_table_save')->name('pasca_ed_table_save');
            Route::post('pasca/standar', 'App\Http\Controllers\PascaAuditController@ks_table_save')->name('pasca_ks_table_save');
            Route::get('pasca/confirm/{id}', 'App\Http\Controllers\PascaAuditController@confirm')->name('pasca_confirm');
            Route::get('pasca/cancel_confirm/{id}', 'App\Http\Controllers\PascaAuditController@cancel_confirm')->name('pasca_cancel_confirm');
        }
    );


    Route::group(
        ['middleware' => 'kajur_koorprodi'],
        function () {
            Route::get('evaluasi/delete/{id_evaluasi}', 'App\Http\Controllers\EDController@delete')->name('ed_delete');
            Route::post('evaluasi/change', 'App\Http\Controllers\EDController@change_action')->name('ed_change_action');

            Route::get('standar/delete/{id_standar}', 'App\Http\Controllers\KSController@delete')->name('ks_delete');
            Route::post('standar/change', 'App\Http\Controllers\KSController@change_action')->name('ks_change_action');
        }
    );


    Route::group(
        ['middleware' => 'kajur_koorprodi_auditor'],
        function () {
            Route::post('evaluasi', 'App\Http\Controllers\EDController@add_action')->name('ed_import_action');
            Route::post('standar', 'App\Http\Controllers\KSController@add_action')->name('ks_import_action');
        }
    );

    Route::group(
        ['middleware' => 'pjm_kajur_koorprodi'],
        function () {
            // Evaluasi Diri
            Route::get('evaluasi', 'App\Http\Controllers\EDController@home')->name('ed_home');
            Route::get('evaluasi/table/{id_evaluasi}', 'App\Http\Controllers\EDController@table')->name('ed_table');
            Route::post('evaluasi/export', 'App\Http\Controllers\EDController@export_all')->name('ed_export_all');
            Route::post('evaluasi/export/file', 'App\Http\Controllers\EDController@export_file')->name('ed_export_file');

            // Ketercapaian Standar
            Route::get('standar', 'App\Http\Controllers\KSController@home')->name('ks_home');
            Route::get('standar/table/{id_standar}', 'App\Http\Controllers\KSController@table')->name('ks_table');
            Route::post('standar/export', 'App\Http\Controllers\KSController@export_all')->name('ks_export_all');
            Route::post('standar/export/file', 'App\Http\Controllers\KSController@export_file')->name('ks_export_file');
        }
    );


    // Deadline End
    Route::get('evaluasi/set_time/{id}', 'App\Http\Controllers\EDDeadlineController@set_time_action_end')->name('ed_set_time_action_end');
    Route::get('standar/set_time/{id}', 'App\Http\Controllers\KSDeadlineController@set_time_action_end')->name('ks_set_time_action_end');


    // Statistik Evaluasi Diri
    Route::get('ed_chart', 'App\Http\Controllers\EDChartController@home')->name('ed_chart');
    Route::post('ed_chart', 'App\Http\Controllers\EDChartController@home')->name('ed_chart_post');


    // Statistik Ketercapaian Standar
    Route::get('ks_chart', 'App\Http\Controllers\KSChartController@home')->name('ks_chart');
    Route::post('ks_chart', 'App\Http\Controllers\KSChartController@home')->name('ks_chart_post');


    // Panduan
    Route::get('panduan', 'App\Http\Controllers\PanduanController@index_others')->name('panduan_home');
    Route::get('panduan/download/{id}', 'App\Http\Controllers\PanduanController@download')->name('panduan_download');
    Route::get('panduan/{id}', 'App\Http\Controllers\PanduanController@show')->name('panduan_detail');


    // Ubah password
    Route::get('password/{id}', 'App\Http\Controllers\AuthenticationController@change_pass')->name('change_password');
    Route::post('password', 'App\Http\Controllers\AuthenticationController@change_pass_action')->name('change_password_action');


    // Pengumuman
    Route::post('pengumuman/close', 'App\Http\Controllers\PengumumanController@close')->name('close_pengumuman');


    // Logout
    Route::get('logout', 'App\Http\Controllers\AuthenticationController@logout')->name('logout');


});

Route::get('tes', function () {
    return view('tes');
})->name('tes');