<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function login_action(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        
        $credential = $request->only('email', 'password');

        if (Auth::attempt($credential)) {
            $user = Auth::user();
            if ($user->role_id == 1) {
                return redirect()->intended('pjm');
            } elseif ($user->role_id == 2) {
                return redirect()->intended('kajur');
            } elseif ($user->role_id == 3) {
                return redirect()->intended('koorprodi');
            } elseif ($user->role_id == 4) {
                return redirect()->intended('auditor');
            }
            return redirect('login');
        }
        return redirect('login')->withErrors(['login_gagal' => 'These credentials do not match our records']);
    }

    public function logout(Request $request) 
    {
        $request->session()->flush();
        Auth::logout();
        return Redirect('login');
    }
}