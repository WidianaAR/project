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
                'id'=>1,
                'jurusan_id'=>2,
                'nama_prodi'=>'Fisika',
            ],
            [
                'id'=>2,
                'jurusan_id'=>1,
                'nama_prodi'=>'Matematika',
            ],
            [
                'id'=>3,
                'jurusan_id'=>3,
                'nama_prodi'=>'Teknik Mesin',
            ],
            [
                'id'=>4,
                'jurusan_id'=>3,
                'nama_prodi'=>'Teknik Elektro',
            ],
            [
                'id'=>5,
                'jurusan_id'=>3,
                'nama_prodi'=>'Teknik Kimia',
            ],
            [
                'id'=>6,
                'jurusan_id'=>5,
                'nama_prodi'=>'Teknik Material dan Metalurgi',
            ],
            [
                'id'=>7,
                'jurusan_id'=>4,
                'nama_prodi'=>'Teknik Sipil',
            ],
            [
                'id'=>8,
                'jurusan_id'=>4,
                'nama_prodi'=>'Perencanaan Wilayah dan Kota',
            ],
            [
                'id'=>9,
                'jurusan_id'=>2,
                'nama_prodi'=>'Teknik Perkapalan',
            ],
            [
                'id'=>10,
                'jurusan_id'=>1,
                'nama_prodi'=>'Sistem Informasi',
            ],
            [
                'id'=>11,
                'jurusan_id'=>1,
                'nama_prodi'=>'Informatika',
            ],
            [
                'id'=>12,
                'jurusan_id'=>3,
                'nama_prodi'=>'Teknik Industri',
            ],
            [
                'id'=>13,
                'jurusan_id'=>5,
                'nama_prodi'=>'Teknik Lingkungan',
            ],
            [
                'id'=>14,
                'jurusan_id'=>2,
                'nama_prodi'=>'Teknik Kelautan',
            ],
            [
                'id'=>15,
                'jurusan_id'=>2,
                'nama_prodi'=>'Teknologi Pangan',
            ],
            [
                'id'=>16,
                'jurusan_id'=>1,
                'nama_prodi'=>'Ilmu Aktuaria',
            ],
            [
                'id'=>17,
                'jurusan_id'=>1,
                'nama_prodi'=>'Statistika',
            ],
            [
                'id'=>18,
                'jurusan_id'=>1,
                'nama_prodi'=>'Bisnis Digital',
            ],
            [
                'id'=>19,
                'jurusan_id'=>4,
                'nama_prodi'=>'Arsitektur',
            ],
            [
                'id'=>20,
                'jurusan_id'=>3,
                'nama_prodi'=>'Rekayasa Keselamatan',
            ],
            [
                'id'=>21,
                'jurusan_id'=>3,
                'nama_prodi'=>'Teknik Logistik',
            ],
            // [
            //     'id'=>22,
            //     'jurusan_id'=>,
            //     'nama_prodi'=>'Desain Komunikasi Visual',
            // ],
        ];

        foreach ($prodi as $value) {
            Prodi::create($value);
        }
    }
}
