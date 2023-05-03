<?php

namespace Tests\Unit;

use App\Models\Panduan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanduanTest extends TestCase
{
    use RefreshDatabase;

    private function pjm_login()
    {
        $pjm = User::find(1);
        $this->actingAs($pjm);
    }

    // Model test
    public function test_panduan_can_be_created()
    {
        $panduan = Panduan::create([
            'judul' => 'Ini judul baru 2',
            'keterangan' => 'Ini keterangan baru',
            'file_data' => 'panduan.pdf'
        ]);

        $this->assertEquals('Ini Judul Baru 2', $panduan->judul);
        $this->assertEquals('Ini keterangan baru', $panduan->keterangan);
        $this->assertEquals('panduan.pdf', $panduan->file_data);
    }

    // Controller test
    public function test_page_displays_a_list_of_panduans()
    {
        $this->pjm_login();
        $response = $this->get(route('panduans.index'));
        $panduans = Panduan::latest()->paginate(8);
        $response->assertViewIs('panduan.home_pjm')->assertViewHas('panduans', $panduans);
    }
}