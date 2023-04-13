<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Prodi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function user()
    {
        $users = User::with(['role', 'jurusan', 'prodi'])->latest()->get();
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

        $jurusan_id = ($request->role_id == 3) ? Prodi::find($request->prodi_id)->jurusan_id : $request->jurusan_id;
        User::create([
            'role_id' => $request->role_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'jurusan_id' => $jurusan_id,
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
        $users = User::where('role_id', 1)->latest()->get()->load('role');
        return view('user.home', compact('users'));
    }

    public function user_kajur()
    {
        $users = User::where('role_id', 2)->latest()->get()->load('role', 'jurusan');
        return view('user.home', compact('users'));
    }

    public function user_koorprodi()
    {
        $users = User::where('role_id', 3)->latest()->get()->load('role', 'jurusan', 'prodi');
        return view('user.home', compact('users'));
    }

    public function user_auditor()
    {
        $users = User::where('role_id', 4)->latest()->get()->load('role');
        return view('user.home', compact('users'));
    }
}