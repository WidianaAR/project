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
                'id' => 1,
                'role_name' => 'PJM',
            ],
            [
                'id' => 2,
                'role_name' => 'Kajur',
            ],
            [
                'id' => 3,
                'role_name' => 'Koorprodi',
            ],
            [
                'id' => 4,
                'role_name' => 'Auditor',
            ],
        ];

        foreach ($role as $value) {
            Role::create($value);
        }
    }
}