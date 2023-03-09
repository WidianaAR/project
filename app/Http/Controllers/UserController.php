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
        ], [
            'email.required' => 'Email harus diisi.',
            'password.required' => 'Kata sandi harus diisi.',
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
        return redirect('login')->withErrors(['login_gagal' => 'Akun tidak terdaftar di dalam sistem']);
    }

    public function logout(Request $request) 
    {
        $request->session()->flush();
        Auth::logout();
        return Redirect('login');
    }

    public function user() 
    {
        $users = User::all();
        return view('user.user', compact('users'));
    }

    public function delete_user($id) 
    {
        User::where('id', $id)->delete();
        return redirect()->route('user')->with('success', 'Data User Berhasil Dihapus');
    }

    public function add_user() 
    {
        $prodis = Prodi::all();
        $jurusans = Jurusan::all();
        return view('user.user_form', compact('prodis', 'jurusans'));
    }

    public function add_user_action(Request $request) 
    {
        $request->validate([
            'role_id' => 'required',
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'password_confirm' => 'required|same:password',
        ]);

        $user = new User([
            'role_id' => $request->role_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'jurusan_id' => $request->jurusan_id,
            'prodi_id' => $request->prodi_id,
        ]);
        $user->save();

        return redirect()->route('user')->with('success', 'Data User Berhasil Ditambahkan');
    }

    public function change_user($id) 
    {
        $prodis = Prodi::all();
        $jurusans = Jurusan::all();
        $user = User::find($id);
        return view('user.user_edit', compact('user', 'prodis', 'jurusans'));
    }

    public function change_user_action(Request $request) 
    {
        $request->validate([
            'role_id' => 'required',
            'name' => 'required',
            'email' => 'required',
        ]);

        $selection = User::find($request->id);
        $selection->update($request->all());

        return redirect()->route('user')->with('success', 'Data User Berhasil Diubah');
    }

    public function user_pjm()
    {
        $users = User::where('role_id', 1)->get();
        return view('user.user', compact('users'));
    }

    public function user_kajur()
    {
        $users = User::where('role_id', 2)->get();
        return view('user.user', compact('users'));
    }

    public function user_koorprodi()
    {
        $users = User::where('role_id', 3)->get();
        return view('user.user', compact('users'));
    }

    public function user_auditor()
    {
        $users = User::where('role_id', 4)->get();
        return view('user.user', compact('users'));
    }
}