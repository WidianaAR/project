<?php

namespace Tests\Unit;

use App\Models\Jurusan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class JurusanTest extends TestCase
{
    use RefreshDatabase;

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
    public function test_input_validation()
    {
        $data = [
            'kode_jurusan' => '7',
            'nama_jurusan' => 'JV',
            'keterangan' => 'Jurusan Validasi'
        ];
        $data2 = [
            'kode_jurusan' => '1',
            'nama_jurusan' => 'JMTI',
            'keterangan' => 'Jurusan Matematika dan Teknologi Informasi'
        ];

        $validator = Validator::make($data, [
            'kode_jurusan' => 'unique:jurusans',
            'nama_jurusan' => 'unique:jurusans',
            'keterangan' => 'unique:jurusans'
        ]);
        $validator2 = Validator::make($data2, [
            'kode_jurusan' => 'unique:jurusans',
            'nama_jurusan' => 'unique:jurusans',
            'keterangan' => 'unique:jurusans'
        ]);

        $this->assertTrue($validator->passes());
        $this->assertFalse($validator2->passes());
    }
}