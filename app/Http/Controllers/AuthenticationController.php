<?php

namespace App\Http\Controllers;

use App\Models\User;
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
                activity()->log('PJM login');
                return redirect()->intended('pjm');
            } elseif ($user->role_id == 2) {
                activity()->log('Kajur login');
                return redirect()->intended('kajur');
            } elseif ($user->role_id == 3) {
                activity()->log('Koorprodi login');
                return redirect()->intended('koorprodi');
            } elseif ($user->role_id == 4) {
                activity()->log('Auditor login');
                return redirect()->intended('auditor');
            }
            return redirect('login');
        }
        return back()->withErrors(['login_gagal' => 'Email atau password salah']);
    }

    public function logout(Request $request)
    {
        activity()->log('User logout');
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
        if (!Hash::check($request->old_pass, $user->password)) {
            return back()->withErrors(['old_pass' => 'Password lama salah!']);
        } elseif (Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password baru tidak boleh sama dengan password lama.']);
        }

        $request->validate([
            'conf_pass' => 'required|same:password'
        ], [
                'conf_pass.same' => 'Password tidak sama, mohon periksa kembali input Anda.'
            ]);

        $data = User::find($user->id);
        $data->update(['password' => Hash::make($request->password)]);
        activity()
            ->performedOn($data)
            ->log('User mengubah password');
        return redirect()->route('ks_chart')->with('success', 'Password akun berhasil diubah.');
    }
}