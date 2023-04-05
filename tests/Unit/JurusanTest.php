<?php

namespace Tests\Unit;

use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JurusanTest extends TestCase
{
    use RefreshDatabase;

    private function pjm_login()
    {
        $pjm = User::find(1);
        $this->actingAs($pjm);
    }

    // Model test
    public function test_jurusan_can_be_created()
    {
        $jurusan = Jurusan::create([
            'kode_jurusan' => 10,
            'nama_jurusan' => 'JB',
            'keterangan' => 'Jurusan Baru'
        ]);

        $this->assertEquals(10, $jurusan->kode_jurusan);
        $this->assertEquals('JB', $jurusan->nama_jurusan);
        $this->assertEquals('Jurusan Baru', $jurusan->keterangan);
    }

    // Controller test
    public function test_page_displays_a_list_of_jurusans()
    {
        $jurusans = Jurusan::all();
        $this->pjm_login();
        $this->get(route('jurusans.index'))->assertViewIs('jurusan.home')->assertViewHas('jurusans', $jurusans);
    }
}