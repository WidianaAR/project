<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private function pjm_login()
    {
        $pjm = User::find(1);
        $this->actingAs($pjm);
    }

    // Model test
    public function test_user_can_be_created()
    {
        $user = User::create([
            'role_id' => 1,
            'name' => 'New User',
            'email' => 'new@gmail.com',
            'password' => 'SimjamuTest123',
        ]);

        $this->assertEquals('1', $user->role_id);
        $this->assertEquals('New User', $user->name);
        $this->assertEquals('new@gmail.com', $user->email);
    }

    // Controller test
    public function test_input_validation()
    {
        $data = [
            'name' => 'New User',
            'email' => 'new@gmail.com',
            'password' => 'SimjamuTest123',
            'confirm' => 'SimjamuTest123',
        ];
        $data2 = [
            'name' => 'Ini akun PJM',
            'email' => 'pjm@gmail.com',
            'password' => 'SimjamuTest123',
            'confirm' => 'SimjamuTest',
        ];

        $validator = Validator::make($data, [
            'name' => 'unique:users',
            'email' => 'email|unique:users',
            'password' => 'min:8|string',
            'confirm' => 'same:password',
        ]);
        $validator2 = Validator::make($data2, [
            'name' => 'unique:users',
            'email' => 'email|unique:users',
            'password' => 'min:8|string',
            'confirm' => 'same:password',
        ]);

        $this->assertTrue($validator->passes());
        $this->assertFalse($validator2->passes());
    }
}