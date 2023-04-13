<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function login_action(Request $request)
    {
        $credential = $request->validate([
            'email' => 'required|email:dns',
            'password' => 'required',
        ]);

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
        return back()->withErrors(['login_gagal' => 'Akun tidak terdaftar di dalam sistem']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }

    public function change_pass($id)
    {
        return view('change_password', ['user' => User::find($id)]);
    }

    public function change_pass_action(Request $request)
    {
        $user = Auth::user();
        if (Hash::check($request->old_pass, $user->password) == false) {
            return back()->withErrors(['old_pass' => 'Password lama salah!']);
        } elseif (Hash::check($request->password, $user->password) == true) {
            return back()->withErrors(['password' => 'Password baru tidak boleh sama dengan password lama.']);
        }

        $request->validate([
            'conf_pass' => 'required|same:password'
        ], [
                'conf_pass.same' => 'Password tidak sama, mohon periksa kembali input Anda.'
            ]);

        User::find($user->id)->update(['password' => Hash::make($request->password)]);
        return redirect()->route('ks_chart')->with('success', 'Password akun berhasil diubah.');
    }
}