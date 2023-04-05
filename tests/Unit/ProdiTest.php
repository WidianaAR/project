<?php

namespace Tests\Unit;

use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProdiTest extends TestCase
{
    use RefreshDatabase;

    private function pjm_login()
    {
        $pjm = User::find(1);
        $this->actingAs($pjm);
    }

    // Model test
    public function test_prodi_can_be_created()
    {
        $prodi = Prodi::create([
            'kode_prodi' => 30,
            'jurusan_id' => 1,
            'nama_prodi' => 'Prodi baru',
        ]);

        $this->assertEquals(30, $prodi->kode_prodi);
        $this->assertEquals(1, $prodi->jurusan_id);
        $this->assertEquals('Prodi Baru', $prodi->nama_prodi);
    }

    // Controller test
    public function test_page_displays_a_list_of_prodis()
    {
        $prodis = Prodi::all();
        $this->pjm_login();
        $this->get(route('prodis.index'))->assertViewIs('prodi.home')->assertViewHas('prodis', $prodis);
    }
}