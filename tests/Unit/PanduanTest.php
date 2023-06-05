<?php

namespace Tests\Unit;

use App\Models\Panduan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanduanTest extends TestCase
{
    use RefreshDatabase;

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
}