<?php

namespace Tests\Feature;

use App\Models\Dokumen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageTahapTest extends TestCase
{
    use RefreshDatabase;
    private function pjm_login()
    {
        $pjm = User::find(1);
        $this->actingAs($pjm);
    }

    public function test_list_of_tahaps()
    {
        $this->pjm_login();
        $response = $this->get('evaluasi');
        $tahaps = Dokumen::where('kategori', 'evaluasi')->with('prodi.jurusan', 'prodi', 'status', 'tahap')->latest('updated_at')->paginate(8);
        $response->assertViewIs('evaluasi_diri.home')->assertViewHas('data', $tahaps);
    }
}