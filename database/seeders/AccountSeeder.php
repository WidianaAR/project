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
                'id_role'=>1,
                'name'=>'ini akun PJM',
                'email'=>'pjm@gmail.com',
                'password'=> bcrypt('12345'),
            ],
            [
                'id_role'=>2,
                'id_jurusan' => 1,
                'name'=>'ini akun Kajur',
                'email'=>'kajur@gmail.com',
                'password'=> bcrypt('12345'),
            ],
            [
                'id_role'=>3,
                'id_jurusan' => 1,
                'id_prodi' => 11,
                'name'=>'ini akun Koorprodi',
                'email'=>'koorprodi@gmail.com',
                'password'=> bcrypt('12345'),
            ],
            [
                'id_role'=>4,
                'name'=>'ini akun Auditor',
                'email'=>'auditor@gmail.com',
                'password'=> bcrypt('12345'),
            ],
            [
                'id_role'=>3,
                'name'=>'ini akun Testing',
                'email'=>'widia.war@gmail.com',
                'password'=> bcrypt('12345'),
            ],
        ];

        foreach ($user as $value) {
            User::create($value);
        }
    }
}
