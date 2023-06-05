<?php

namespace Tests\Unit;

use App\Models\Pengumuman;
use App\Models\PengumumanUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengumumanTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    use RefreshDatabase;

    private function pjm_login()
    {
        $pjm = User::find(1);
        $this->actingAs($pjm);
    }

    // Model test
    public function test_pengumuman_can_be_created()
    {
        $pengumuman = Pengumuman::create([
            'judul' => 'Pengumuman Test',
            'isi' => 'ini pengumuman'
        ]);

        $this->assertEquals('Pengumuman Test', $pengumuman->judul);
        $this->assertEquals('ini pengumuman', $pengumuman->isi);
    }

    public function test_pengumuman_user_can_be_created()
    {
        $pengumuman = Pengumuman::create([
            'judul' => 'Pengumuman Test',
            'isi' => 'ini pengumuman'
        ]);

        $pengumuman = PengumumanUser::create([
            'user_id' => 1,
            'pengumuman_id' => 2
        ]);

        $this->assertEquals(1, $pengumuman->user_id);
        $this->assertEquals(2, $pengumuman->pengumuman_id);
    }
}