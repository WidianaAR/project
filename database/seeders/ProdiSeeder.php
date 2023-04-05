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
                'kode_prodi' => 1,
                'jurusan_id' => 2,
                'nama_prodi' => 'Fisika',
            ],
            [
                'kode_prodi' => 2,
                'jurusan_id' => 1,
                'nama_prodi' => 'Matematika',
            ],
            [
                'kode_prodi' => 3,
                'jurusan_id' => 3,
                'nama_prodi' => 'Teknik Mesin',
            ],
            [
                'kode_prodi' => 4,
                'jurusan_id' => 3,
                'nama_prodi' => 'Teknik Elektro',
            ],
            [
                'kode_prodi' => 5,
                'jurusan_id' => 3,
                'nama_prodi' => 'Teknik Kimia',
            ],
            [
                'kode_prodi' => 6,
                'jurusan_id' => 5,
                'nama_prodi' => 'Teknik Material dan Metalurgi',
            ],
            [
                'kode_prodi' => 7,
                'jurusan_id' => 4,
                'nama_prodi' => 'Teknik Sipil',
            ],
            [
                'kode_prodi' => 8,
                'jurusan_id' => 4,
                'nama_prodi' => 'Perencanaan Wilayah dan Kota',
            ],
            [
                'kode_prodi' => 9,
                'jurusan_id' => 2,
                'nama_prodi' => 'Teknik Perkapalan',
            ],
            [
                'kode_prodi' => 10,
                'jurusan_id' => 1,
                'nama_prodi' => 'Sistem Informasi',
            ],
            [
                'kode_prodi' => 11,
                'jurusan_id' => 1,
                'nama_prodi' => 'Informatika',
            ],
            [
                'kode_prodi' => 12,
                'jurusan_id' => 3,
                'nama_prodi' => 'Teknik Industri',
            ],
            [
                'kode_prodi' => 13,
                'jurusan_id' => 5,
                'nama_prodi' => 'Teknik Lingkungan',
            ],
            [
                'kode_prodi' => 14,
                'jurusan_id' => 2,
                'nama_prodi' => 'Teknik Kelautan',
            ],
            [
                'kode_prodi' => 15,
                'jurusan_id' => 2,
                'nama_prodi' => 'Teknologi Pangan',
            ],
            [
                'kode_prodi' => 16,
                'jurusan_id' => 1,
                'nama_prodi' => 'Ilmu Aktuaria',
            ],
            [
                'kode_prodi' => 17,
                'jurusan_id' => 1,
                'nama_prodi' => 'Statistika',
            ],
            [
                'kode_prodi' => 18,
                'jurusan_id' => 1,
                'nama_prodi' => 'Bisnis Digital',
            ],
            [
                'kode_prodi' => 19,
                'jurusan_id' => 4,
                'nama_prodi' => 'Arsitektur',
            ],
            [
                'kode_prodi' => 20,
                'jurusan_id' => 3,
                'nama_prodi' => 'Rekayasa Keselamatan',
            ],
            [
                'kode_prodi' => 21,
                'jurusan_id' => 3,
                'nama_prodi' => 'Teknik Logistik',
            ],
            // [
            //     'kode_prodi'=>22,
            //     'jurusan_id'=>,
            //     'nama_prodi'=>'Desain Komunikasi Visual',
            // ],
        ];

        foreach ($prodi as $value) {
            Prodi::create($value);
        }
    }
}