<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Prodi;
use App\Models\Role;
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

    public function user()
    {
        $users = User::with(['role', 'jurusan', 'prodi'])->get();
        return view('user.home', compact('users'));
    }

    public function delete_user($id)
    {
        User::destroy($id);
        return redirect('user')->with('success', 'Data user berhasil dihapus');
    }

    public function add_user()
    {
        $prodis = Prodi::all();
        $jurusans = Jurusan::all();
        $roles = Role::all();
        return view('user.add_form', compact('prodis', 'jurusans', 'roles'));
    }

    public function add_user_action(Request $request)
    {
        $request->validate([
            'role_id' => 'required',
            'name' => 'required|unique:users',
            'email' => 'required|email:dns|unique:users',
            'password' => 'required',
            'confirm' => 'required|same:password',
        ], [
                'name.unique' => 'Nama user sudah terdaftar!',
                'email.unique' => 'Email user sudah terdaftar!',
                'confirm.same' => 'Password tidak sama, mohon periksa kembali password Anda.'
            ]);

        User::create([
            'role_id' => $request->role_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'jurusan_id' => $request->jurusan_id,
            'prodi_id' => $request->prodi_id,
        ]);

        return redirect('user')->with('success', 'Data user berhasil ditambahkan');
    }

    public function change_user($id)
    {
        $prodis = Prodi::all();
        $jurusans = Jurusan::all();
        $roles = Role::all();
        $user = User::find($id);
        return view('user.change_form', compact('user', 'prodis', 'jurusans', 'roles'));
    }

    public function change_user_action(Request $request, User $user)
    {
        $rules = [
            'role_id' => 'required',
            'name' => 'required',
            'email' => 'required',
        ];

        if ($request->name != $user->name) {
            $rules['name'] = 'required|unique:users';
        } elseif ($request->email != $user->email) {
            $rules['email'] = 'required|unique:users';
        }

        $data = $request->validate($rules, [
            'name.unique' => 'Nama user sudah terdaftar!',
            'email.unique' => 'Email user sudah terdaftar!'
        ]);

        User::find($user->id)->update($data);
        return redirect('user')->with('success', 'Data user berhasil diubah');
    }

    public function user_pjm()
    {
        $users = User::where('role_id', 1)->get()->load('role');
        return view('user.home', compact('users'));
    }

    public function user_kajur()
    {
        $users = User::where('role_id', 2)->get()->load('role', 'jurusan');
        return view('user.home', compact('users'));
    }

    public function user_koorprodi()
    {
        $users = User::where('role_id', 3)->get()->load('role', 'jurusan', 'prodi');
        return view('user.home', compact('users'));
    }

    public function user_auditor()
    {
        $users = User::where('role_id', 4)->get()->load('role');
        return view('user.home', compact('users'));
    }

    public function change_pass($id)
    {
        return view('change_password', ['user' => User::find($id)]);
    }

    public function change_pass_action(Request $request)
    {
        if (Hash::check($request->old_pass, Auth::user()->password) == false) {
            return back()->withErrors(['old_pass' => 'Password lama salah!']);
        } elseif (Hash::check($request->password, Auth::user()->password) == true) {
            return back()->withErrors(['password' => 'Password baru tidak boleh sama dengan password lama.']);
        }

        $request->validate([
            'conf_pass' => 'required|same:password'
        ], [
                'conf_pass.same' => 'Password tidak sama, mohon periksa kembali input Anda.'
            ]);

        User::find(Auth::user()->id)->update(['password' => Hash::make($request->password)]);
        return redirect()->route('ks_chart')->with('success', 'Password akun berhasil diubah.');
    }
}