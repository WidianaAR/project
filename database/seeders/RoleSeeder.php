<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = [
            [
                'id_role'=>1,
                'role_name'=>'pjm',
            ],
            [
                'id_role'=>2,
                'role_name'=>'kajur',
            ],
            [
                'id_role'=>3,
                'role_name'=>'koorprodi',
            ],
            [
                'id_role'=>4,
                'role_name'=>'auditor',
            ],
        ];

        foreach ($role as $value) {
            Role::create($value);
        }
    }
}
