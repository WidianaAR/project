<?php

namespace Database\Seeders;

use App\Models\UserAccessFile;
use Illuminate\Database\Seeder;

class UserAccessFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'user_id' => 2,
                'jurusan_id' => 1
            ],
            [
                'user_id' => 3,
                'jurusan_id' => 1,
                'prodi_id' => 11
            ],
            [
                'user_id' => 4,
                'prodi_id' => 11
            ],
            [
                'user_id' => 4,
                'prodi_id' => 10
            ],
            [
                'user_id' => 5,
                'jurusan_id' => 1,
                'prodi_id' => 10
            ],
        ];

        foreach ($data as $value) {
            UserAccessFile::create($value);
        }
    }
}