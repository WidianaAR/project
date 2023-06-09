<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jurusan;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jurusan = [
            [
                'kode_jurusan' => 1,
                'nama_jurusan' => 'JMTI',
                'keterangan' => 'Jurusan Matematika dan Teknologi Informasi',
            ],
            [
                'kode_jurusan' => 2,
                'nama_jurusan' => 'JSTPK',
                'keterangan' => 'Jurusan Sains, Teknologi Pangan, dan Kemaritiman',
            ],
            [
                'kode_jurusan' => 3,
                'nama_jurusan' => 'JTIP',
                'keterangan' => 'Jurusan Teknologi Industri dan Proses',
            ],
            [
                'kode_jurusan' => 4,
                'nama_jurusan' => 'JTSP',
                'keterangan' => 'Jurusan Teknik Sipil dan Perencanaan',
            ],
            [
                'kode_jurusan' => 5,
                'nama_jurusan' => 'JIKL',
                'keterangan' => 'Jurusan Ilmu Kebumian dan Lingkungan',
            ],
            [
                'kode_jurusan' => 6,
                'nama_jurusan' => 'JD',
                'keterangan' => 'Jurusan Dummy',
            ],
        ];

        foreach ($jurusan as $value) {
            Jurusan::create($value);
        }
    }
}