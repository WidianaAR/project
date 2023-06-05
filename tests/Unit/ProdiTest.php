<?php

namespace Tests\Unit;

use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
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
    public function test_input_validation()
    {
        $data = [
            'kode_prodi' => 31,
            'jurusan_id' => 1,
            'nama_prodi' => 'Prodi validasi',
        ];
        $data2 = [
            'kode_prodi' => 11,
            'jurusan_id' => 1,
            'nama_prodi' => 'Informatika',
        ];

        $validator = Validator::make($data, [
            'kode_prodi' => 'unique:prodis',
            'nama_prodi' => 'unique:prodis'
        ]);
        $validator2 = Validator::make($data2, [
            'kode_prodi' => 'unique:prodis',
            'nama_prodi' => 'unique:prodis'
        ]);

        $this->assertTrue($validator->passes());
        $this->assertFalse($validator2->passes());
    }
}