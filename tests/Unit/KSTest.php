<?php

namespace Tests\Unit;

use App\Models\KetercapaianStandar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KSTest extends TestCase
{
    use RefreshDatabase;

    private function pjm_login()
    {
        $pjm = User::find(1);
        $this->actingAs($pjm);
    }

    // Model test
    public function test_ketercapaian_standar_can_be_created()
    {
        $data = KetercapaianStandar::create([
            'file_data' => 'Files/Ketercapaian Standar_Informatika_2023.xlsx',
            'prodi_id' => 11,
            'tahun' => 2023,
            'status' => 'ditinjau'
        ]);

        $this->assertEquals('Files/Ketercapaian Standar_Informatika_2023.xlsx', $data->file_data);
        $this->assertEquals(11, $data->prodi_id);
        $this->assertEquals(2023, $data->tahun);
        $this->assertEquals('ditinjau', $data->status);
    }

    // Controller test
    public function test_page_displays_a_list_of_datas()
    {
        $this->pjm_login();
        $response = $this->get('standar');
        $data = KetercapaianStandar::with('prodi.jurusan', 'prodi')->latest('tahun')->paginate(8);
        $response->assertViewIs('ketercapaian_standar.home')->assertViewHas('data', $data);
    }

    public function test_page_displays_a_list_of_datas_by_year()
    {
        $this->pjm_login();
        $response = $this->get(route('ks_filter_year', 2022));
        $data = KetercapaianStandar::where('tahun', 2022)->with('prodi', 'prodi.jurusan')->latest('tahun')->paginate(8);
        $response->assertViewIs('ketercapaian_standar.home')->assertViewHas('data', $data);
    }

    public function test_page_displays_a_list_of_datas_by_jurusan()
    {
        $this->pjm_login();
        $response = $this->get(route('ks_filter_jurusan', 1));
        $data = KetercapaianStandar::withWhereHas('prodi.jurusan', function ($query) {
            $query->where('id', 1);
        })->with('prodi')->latest('tahun')->paginate(8);
        $response->assertViewIs('ketercapaian_standar.home')->assertViewHas('data', $data);
    }

    public function test_page_displays_a_list_of_datas_by_prodi()
    {
        $this->pjm_login();
        $response = $this->get(route('ks_filter_prodi', 11));
        $data = KetercapaianStandar::where('prodi_id', 11)->with('prodi', 'prodi.jurusan')->latest('tahun')->paginate(8);
        $response->assertViewIs('ketercapaian_standar.home')->assertViewHas('data', $data);
    }
}