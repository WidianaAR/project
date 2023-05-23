<?php

use App\Models\Panduan;
use Illuminate\Support\Facades\Route;
use Spatie\Activitylog\Models\Activity;

Route::redirect('/', '/login');

// Login dan Logout
Route::get('login', 'App\Http\Controllers\AuthenticationController@login')->name('login');
Route::post('login', 'App\Http\Controllers\AuthenticationController@login_action')->name('login_action');
Route::get('logout', 'App\Http\Controllers\AuthenticationController@logout')->name('logout');

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
            // User Filter
            Route::get('user/filter/pjm', 'App\Http\Controllers\UserController@user_pjm')->name('user_pjm');
            Route::get('user/filter/kajur', 'App\Http\Controllers\UserController@user_kajur')->name('user_kajur');
            Route::get('user/filter/koorprodi', 'App\Http\Controllers\UserController@user_koorprodi')->name('user_koorprodi');
            Route::get('user/filter/auditor', 'App\Http\Controllers\UserController@user_auditor')->name('user_auditor');

            // Kelola Evaluasi Diri
            Route::get('evaluasi/set_time', 'App\Http\Controllers\EDDeadlineController@set_time')->name('ed_set_time');
            Route::post('evaluasi/set_time', 'App\Http\Controllers\EDDeadlineController@set_time_action')->name('ed_set_time_action');

            // Kelola Ketercapaian Standar
            Route::get('standar/set_time', 'App\Http\Controllers\KSDeadlineController@set_time')->name('ks_set_time');
            Route::post('standar/set_time', 'App\Http\Controllers\KSDeadlineController@set_time_action')->name('ks_set_time_action');

            // Filter
            Route::get('evaluasi/filter/jurusan/{jurusan_id}', 'App\Http\Controllers\EDController@filter_jurusan')->name('ed_filter_jurusan');
            Route::get('standar/filter/jurusan/{jurusan_id}', 'App\Http\Controllers\KSController@filter_jurusan')->name('ks_filter_jurusan');

            // Kelola Jurusan
            Route::resource('jurusans', 'App\Http\Controllers\JurusanController');

            // Kelola Prodi
            Route::resource('prodis', 'App\Http\Controllers\ProdiController');

            // Kelola Panduan
            Route::resource('panduans', 'App\Http\Controllers\PanduanController');

            // Riwayat Aktivitas
            Route::get('logs', function () {
                $perPage = request()->query('per_page', 5);
                $datas = Activity::with('causer')->latest()->paginate($perPage)->withQueryString();
                return view('log', compact('datas'));
            })->name('logs');

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
        }
    );


    Route::group(
        ['middleware' => 'cek_login:4'],
        function () {
            Route::get('auditor', 'App\Http\Controllers\EDChartController@home');

            Route::get('tilik_auditor/{kategori?}', 'App\Http\Controllers\TilikController@index_auditor')->name('tilik_home_auditor');
            Route::get('tilik_auditor/{kategori?}/{year}', 'App\Http\Controllers\TilikController@filter_year_auditor')->name('tilik_year_auditor');
            Route::post('tilik/evaluasi', 'App\Http\Controllers\TilikController@ed_table_save')->name('tilik_ed_table_save');
            Route::post('tilik/standar', 'App\Http\Controllers\TilikController@ks_table_save')->name('tilik_ks_table_save');

            Route::get('pasca_auditor/{kategori?}', 'App\Http\Controllers\PascaAuditController@index_auditor')->name('pasca_home_auditor');
            Route::get('pasca_auditor/{kategori?}/{year}', 'App\Http\Controllers\PascaAuditController@filter_year_auditor')->name('pasca_year_auditor');
            Route::post('pasca/evaluasi', 'App\Http\Controllers\PascaAuditController@ed_table_save')->name('pasca_ed_table_save');
            Route::post('pasca/standar', 'App\Http\Controllers\PascaAuditController@ks_table_save')->name('pasca_ks_table_save');

            Route::get('pasca/confirm/{idi}', 'App\Http\Controllers\PascaAuditController@confirm')->name('pasca_confirm');
            Route::get('pasca/cancel_confirm/{idi}', 'App\Http\Controllers\PascaAuditController@cancel_confirm')->name('pasca_cancel_confirm');
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
        ['middleware' => 'pjm_kajur'],
        function () {
            Route::get('evaluasi/filter/prodi/{prodi_id}', 'App\Http\Controllers\EDController@filter_prodi')->name('ed_filter_prodi');
            Route::get('standar/filter/prodi/{prodi_id}', 'App\Http\Controllers\KSController@filter_prodi')->name('ks_filter_prodi');
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
            Route::get('evaluasi/filter/year/{year}', 'App\Http\Controllers\EDController@filter_year')->name('ed_filter_year');
            Route::post('evaluasi/export', 'App\Http\Controllers\EDController@export_all')->name('ed_export_all');
            Route::post('evaluasi/export/file', 'App\Http\Controllers\EDController@export_file')->name('ed_export_file');

            // Ketercapaian Standar
            Route::get('standar', 'App\Http\Controllers\KSController@home')->name('ks_home');
            Route::get('standar/table/{id_standar}', 'App\Http\Controllers\KSController@table')->name('ks_table');
            Route::get('standar/filter/year/{year}', 'App\Http\Controllers\KSController@filter_year')->name('ks_filter_year');
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


    Route::get('pengumuman/{id}', 'App\Http\Controllers\PengumumanController@close')->name('close_pengumuman');


    // Tilik
    Route::get('tilik/{kategori?}', 'App\Http\Controllers\TilikController@index')->name('tilik_home');
    Route::get('tilik/{kategori?}/{year}', 'App\Http\Controllers\TilikController@filter_year')->name('tilik_year');
    Route::get('tilik/evaluasi/table/{id}', 'App\Http\Controllers\TilikController@ed_table')->name('tilik_ed_table');
    Route::get('tilik/standar/table/{id}', 'App\Http\Controllers\TilikController@ks_table')->name('tilik_ks_table');


    // Pasca Audit
    Route::get('pasca/{kategori?}', 'App\Http\Controllers\PascaAuditController@index')->name('pasca_home');
    Route::get('pasca/{kategori?}/{year}', 'App\Http\Controllers\PascaAuditController@filter_year')->name('pasca_year');
    Route::get('pasca/evaluasi/table/{id}', 'App\Http\Controllers\PascaAuditController@ed_table')->name('pasca_ed_table');
    Route::get('pasca/standar/table/{id}', 'App\Http\Controllers\PascaAuditController@ks_table')->name('pasca_ks_table');
});