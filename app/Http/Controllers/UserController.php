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
        $users = User::with(['role', 'jurusan', 'prodi'])->latest()->paginate(8);
        return view('user.home', compact('users'));
    }

    public function delete_user($id)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect('user')->with('error', 'Data user tidak ditemukan');
        }

        activity()
            ->performedOn($user)
            ->log('Menghapus data user ' . $user->name);
        $user->delete();
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
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'confirm' => 'required|same:password',
        ], [
                'name.unique' => 'Nama user sudah terdaftar!',
                'email.unique' => 'Email user sudah terdaftar!',
                'confirm.same' => 'Password tidak sama, mohon periksa kembali password Anda.'
            ]);

        $jurusan_id = ($request->role_id == 3) ? Prodi::find($request->prodi_id)->jurusan_id : $request->jurusan_id;
        $user = User::create([
            'role_id' => $request->role_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'jurusan_id' => $jurusan_id,
            'prodi_id' => $request->prodi_id,
        ]);

        activity()
            ->performedOn($user)
            ->log('Menambahkan data user ' . $user->name);
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

    public function change_user_action(Request $request, $id_user)
    {
        $user = User::find($id_user);
        $rules = [
            'role_id' => 'required',
            'name' => 'required',
            'email' => 'required|email',
        ];

        if ($request->name != $user->name) {
            $rules['name'] = 'required|unique:users';
        }

        if ($request->email != $user->email) {
            $rules['email'] = 'required|unique:users';
        }

        $request->validate($rules, [
            'name.unique' => 'Nama user sudah terdaftar!',
            'email.unique' => 'Email user sudah terdaftar!'
        ]);

        if ($request->role_id == 3) {
            $request->merge(['jurusan_id' => Prodi::find($request->prodi_id)->jurusan_id]);
        }

        User::find($user->id)->update($request->all());
        activity()
            ->performedOn($user)
            ->log('Mengubah data user dengan id ' . $user->id);
        return redirect('user')->with('success', 'Data user berhasil diubah');
    }

    public function user_pjm()
    {
        $users = User::with('role')->where('role_id', 1)->latest()->paginate(8);
        return view('user.home', compact('users'));
    }

    public function user_kajur()
    {
        $users = User::with(['role', 'jurusan'])->where('role_id', 2)->latest()->paginate(8);
        return view('user.home', compact('users'));
    }

    public function user_koorprodi()
    {
        $users = User::with(['role', 'jurusan', 'prodi'])->where('role_id', 3)->latest()->paginate(8);
        return view('user.home', compact('users'));
    }

    public function user_auditor()
    {
        $users = User::with(['role', 'prodi'])->where('role_id', 4)->latest()->paginate(8);
        return view('user.home', compact('users'));
    }
}