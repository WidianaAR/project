<?php

namespace Tests\Unit;

use App\Models\EvaluasiDiri;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EDTest extends TestCase
{
    use RefreshDatabase;

    private function pjm_login()
    {
        $pjm = User::find(1);
        $this->actingAs($pjm);
    }

    // Model test
    public function test_evaluasi_diri_can_be_created()
    {
        $data = EvaluasiDiri::create([
            'file_data' => 'Files/Evaluasi Diri_Informatika_2023.xlsx',
            'prodi_id' => 11,
            'tahun' => 2023,
            'status' => 'ditinjau'
        ]);

        $this->assertEquals('Files/Evaluasi Diri_Informatika_2023.xlsx', $data->file_data);
        $this->assertEquals(11, $data->prodi_id);
        $this->assertEquals(2023, $data->tahun);
        $this->assertEquals('ditinjau', $data->status);
    }

    // Controller test
    public function test_page_displays_a_list_of_datas()
    {
        $this->pjm_login();
        $response = $this->get('evaluasi');
        $data = EvaluasiDiri::with('prodi.jurusan', 'prodi')->latest('tahun')->paginate(8);
        $response->assertViewIs('evaluasi_diri.home')->assertViewHas('data', $data);
    }

    public function test_page_displays_a_list_of_datas_by_year()
    {
        $this->pjm_login();
        $response = $this->get(route('ed_filter_year', 2022));
        $data = EvaluasiDiri::where('tahun', 2022)->with('prodi', 'prodi.jurusan')->latest('tahun')->paginate(8);
        $response->assertViewIs('evaluasi_diri.home')->assertViewHas('data', $data);
    }

    public function test_page_displays_a_list_of_datas_by_jurusan()
    {
        $this->pjm_login();
        $response = $this->get(route('ed_filter_jurusan', 1));
        $data = EvaluasiDiri::withWhereHas('prodi.jurusan', function ($query) {
            $query->where('id', 1);
        })->with('prodi')->latest('tahun')->paginate(8);
        $response->assertViewIs('evaluasi_diri.home')->assertViewHas('data', $data);
    }

    public function test_page_displays_a_list_of_datas_by_prodi()
    {
        $this->pjm_login();
        $response = $this->get(route('ed_filter_prodi', 11));
        $data = EvaluasiDiri::where('prodi_id', 11)->with('prodi', 'prodi.jurusan')->latest('tahun')->paginate(8);
        $response->assertViewIs('evaluasi_diri.home')->assertViewHas('data', $data);
    }
}