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
                'id'=>1,
                'nama_jurusan'=>'JMTI',
                'keterangan' => 'Jurusan Matematika dan Teknologi Informasi',
            ],
            [
                'id'=>2,
                'nama_jurusan'=>'JSTPK',
                'keterangan' => 'Jurusan Sains, Teknologi Pangan, dan Kemaritiman',
            ],
            [
                'id'=>3,
                'nama_jurusan'=>'JTIP',
                'keterangan' => 'Jurusan Teknologi Industri dan Proses',
            ],
            [
                'id'=>4,
                'nama_jurusan'=>'JTSP',
                'keterangan' => 'Jurusan Teknik Sipil dan Perencanaan',
            ],
            [
                'id'=>5,
                'nama_jurusan'=>'JIKL',
                'keterangan' => 'Jurusan Ilmu Kebumian dan Lingkungan',
            ],
        ];

        foreach ($jurusan as $value) {
            Jurusan::create($value);
        }
    }
}
