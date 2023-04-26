<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            [
                'role_id' => 1,
                'name' => 'ini akun PJM',
                'email' => 'pjm@gmail.com',
                'password' => bcrypt('12345'),
            ],
            [
                'role_id' => 2,
                'jurusan_id' => 1,
                'name' => 'ini akun Kajur',
                'email' => 'kajur@gmail.com',
                'password' => bcrypt('12345'),
            ],
            [
                'role_id' => 3,
                'jurusan_id' => 1,
                'prodi_id' => 11,
                'name' => 'ini akun Koorprodi',
                'email' => 'koorprodi@gmail.com',
                'password' => bcrypt('12345'),
            ],
            [
                'role_id' => 4,
                'jurusan_id' => 1,
                'prodi_id' => 11,
                'name' => 'ini akun Auditor',
                'email' => 'auditor@gmail.com',
                'password' => bcrypt('12345'),
            ],
            [
                'role_id' => 3,
                'jurusan_id' => 1,
                'prodi_id' => 10,
                'name' => 'ini akun Testing',
                'email' => 'widia.war@gmail.com',
                'password' => bcrypt('12345'),
            ],
        ];

        foreach ($user as $value) {
            User::create($value);
        }
    }
}