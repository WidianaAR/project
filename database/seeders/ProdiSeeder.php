<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prodi;

class ProdiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $prodi = [
            [
                'id_prodi'=>1,
                'id_jurusan'=>2,
                'nama_prodi'=>'Fisika',
            ],
            [
                'id_prodi'=>2,
                'id_jurusan'=>1,
                'nama_prodi'=>'Matematika',
            ],
            [
                'id_prodi'=>3,
                'id_jurusan'=>3,
                'nama_prodi'=>'Teknik Mesin',
            ],
            [
                'id_prodi'=>4,
                'id_jurusan'=>3,
                'nama_prodi'=>'Teknik Elektro',
            ],
            [
                'id_prodi'=>5,
                'id_jurusan'=>3,
                'nama_prodi'=>'Teknik Kimia',
            ],
            [
                'id_prodi'=>6,
                'id_jurusan'=>5,
                'nama_prodi'=>'Teknik Material dan Metalurgi',
            ],
            [
                'id_prodi'=>7,
                'id_jurusan'=>4,
                'nama_prodi'=>'Teknik Sipil',
            ],
            [
                'id_prodi'=>8,
                'id_jurusan'=>4,
                'nama_prodi'=>'Perencanaan Wilayah dan Kota',
            ],
            [
                'id_prodi'=>9,
                'id_jurusan'=>2,
                'nama_prodi'=>'Teknik Perkapalan',
            ],
            [
                'id_prodi'=>10,
                'id_jurusan'=>1,
                'nama_prodi'=>'Sistem Informasi',
            ],
            [
                'id_prodi'=>11,
                'id_jurusan'=>1,
                'nama_prodi'=>'Informatika',
            ],
            [
                'id_prodi'=>12,
                'id_jurusan'=>3,
                'nama_prodi'=>'Teknik Industri',
            ],
            [
                'id_prodi'=>13,
                'id_jurusan'=>5,
                'nama_prodi'=>'Teknik Lingkungan',
            ],
            [
                'id_prodi'=>14,
                'id_jurusan'=>2,
                'nama_prodi'=>'Teknik Kelautan',
            ],
            [
                'id_prodi'=>15,
                'id_jurusan'=>2,
                'nama_prodi'=>'Teknologi Pangan',
            ],
            [
                'id_prodi'=>16,
                'id_jurusan'=>1,
                'nama_prodi'=>'Ilmu Aktuaria',
            ],
            [
                'id_prodi'=>17,
                'id_jurusan'=>1,
                'nama_prodi'=>'Statistika',
            ],
            [
                'id_prodi'=>18,
                'id_jurusan'=>1,
                'nama_prodi'=>'Bisnis Digital',
            ],
            [
                'id_prodi'=>19,
                'id_jurusan'=>4,
                'nama_prodi'=>'Arsitektur',
            ],
            [
                'id_prodi'=>20,
                'id_jurusan'=>3,
                'nama_prodi'=>'Rekayasa Keselamatan',
            ],
            [
                'id_prodi'=>21,
                'id_jurusan'=>3,
                'nama_prodi'=>'Teknik Logistik',
            ],
            // [
            //     'id_prodi'=>22,
            //     'id_jurusan'=>,
            //     'nama_prodi'=>'Desain Komunikasi Visual',
            // ],
        ];

        foreach ($prodi as $value) {
            Prodi::create($value);
        }
    }
}
