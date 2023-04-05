<?php

namespace Tests\Unit;

use App\Models\EvaluasiDiri;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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
            'jurusan_id' => 1,
            'tahun' => 2023,
            'status' => 'ditinjau'
        ]);

        $this->assertEquals('Files/Evaluasi Diri_Informatika_2023.xlsx', $data->file_data);
        $this->assertEquals(11, $data->prodi_id);
        $this->assertEquals(1, $data->jurusan_id);
        $this->assertEquals(2023, $data->tahun);
        $this->assertEquals('ditinjau', $data->status);
    }

    // Controller test
    public function test_page_displays_a_list_of_datas()
    {
        $data = EvaluasiDiri::all();
        $this->pjm_login();
        $this->get('evaluasi')->assertViewIs('evaluasi_diri.home')->assertViewHas('data', $data);
    }

    public function test_page_displays_a_list_of_datas_by_year()
    {
        $data = EvaluasiDiri::where('tahun', 2023)->get();
        $this->pjm_login();
        $this->get(route('ed_filter_year', 2023))->assertViewIs('evaluasi_diri.home')->assertViewHas('data', $data);
    }

    public function test_page_displays_a_list_of_datas_by_jurusan()
    {
        $data = EvaluasiDiri::where('jurusan_id', 1)->get();
        $this->pjm_login();
        $this->get(route('ed_filter_jurusan', 1))->assertViewIs('evaluasi_diri.home')->assertViewHas('data', $data);
    }

    public function test_page_displays_a_list_of_datas_by_prodi()
    {
        $data = EvaluasiDiri::where('prodi_id', 11)->get();
        $this->pjm_login();
        $this->get(route('ed_filter_prodi', 11))->assertViewIs('evaluasi_diri.home')->assertViewHas('data', $data);
    }
}