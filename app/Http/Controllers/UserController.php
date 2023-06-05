<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Prodi;
use App\Models\Role;
use App\Models\User;
use App\Models\UserAccessFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function user()
    {
        $users = User::with(['role', 'user_access_file', 'user_access_file.jurusan', 'user_access_file.prodi'])->latest()->paginate(8);
        return view('user.home', compact('users'));
    }

    public function user_filter($role)
    {
        $users = User::where('role_id', $role)->latest()->paginate(8);
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
            'name' => 'unique:users',
            'email' => 'email|unique:users',
            'password' => 'min:8|string',
            'confirm' => 'same:password',
        ], [
                'name.unique' => 'Nama user sudah terdaftar!',
                'email.unique' => 'Email user sudah terdaftar!',
                'password.min' => 'Password minimal 8 karakter!',
                'confirm.same' => 'Password tidak sama, mohon periksa kembali password Anda.'
            ]);

        $jurusan_id = ($request->role_id == 3) ? Prodi::find($request->prodi_id)->jurusan_id : $request->jurusan_id;
        $user = User::create([
            'role_id' => $request->role_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        if ($request->role_id == 4) {
            foreach ($request->prodi_id_auditor as $prodi) {
                if ($prodi) {
                    UserAccessFile::create([
                        'user_id' => $user->id,
                        'jurusan_id' => $jurusan_id,
                        'prodi_id' => $prodi
                    ]);
                }
            }
        } else {
            UserAccessFile::create([
                'user_id' => $user->id,
                'jurusan_id' => $jurusan_id,
                'prodi_id' => $request->prodi_id
            ]);
        }

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
        $prodi_auditor = ($user->role_id == 4) ? UserAccessFile::where('user_id', $id)->pluck('prodi_id')->toArray() : null;
        return view('user.change_form', compact('user', 'prodis', 'jurusans', 'roles', 'prodi_auditor'));
    }

    public function change_user_action(Request $request, $id_user)
    {
        $user = User::find($id_user);
        $rules = ['email' => 'email'];

        if ($request->name != $user->name) {
            $rules['name'] = 'unique:users';
        }

        if ($request->email != $user->email) {
            $rules['email'] = '|unique:users';
        }

        $request->validate($rules, [
            'name.unique' => 'Nama user sudah terdaftar!',
            'email.unique' => 'Email user sudah terdaftar!'
        ]);

        $access = UserAccessFile::where('user_id', $user->id)->get();
        if ($request->role_id == 3) {
            $jurusan = Prodi::find($request->prodi_id)->jurusan_id;
            if ($user->role_id == 2 || $user->role_id == 3 && $access[0]->prodi_id != $request->prodi_id) {
                $access[0]->update([
                    'prodi_id' => $request->prodi_id,
                    'jurusan_id' => $jurusan
                ]);
            } elseif ($user->role_id == 1) {
                UserAccessFile::create([
                    'user_id' => $request->id,
                    'prodi_id' => $request->prodi_id,
                    'jurusan_id' => $jurusan
                ]);
            } else {
                if ($access->count() > 1) {
                    $access->where('id', '!=', $access[0]->id)->each(function ($userAccessFile) {
                        $userAccessFile->delete();
                    });
                    $access[0]->update([
                        'prodi_id' => $request->prodi_id,
                        'jurusan_id' => $jurusan
                    ]);
                }
            }
        } elseif ($request->role_id == 4) {
            if ($user->role_id == 2 || $user->role_id == 3) {
                $access[0]->update([
                    'prodi_id' => $request->prodi_id_auditor[0],
                    'jurusan_id' => null
                ]);
                if (count($request->prodi_id_auditor) > 1) {
                    foreach ($request->prodi_id_auditor as $prodi) {
                        if ($prodi && $prodi != $access[0]->prodi_id) {
                            UserAccessFile::create([
                                'user_id' => $request->id,
                                'prodi_id' => $prodi,
                                'jurusan_id' => null
                            ]);
                        }
                    }
                }
            } elseif ($user->role_id == 1) {
                foreach ($request->prodi_id_auditor as $prodi) {
                    if ($prodi) {
                        UserAccessFile::create([
                            'user_id' => $request->id,
                            'prodi_id' => $prodi,
                            'jurusan_id' => null
                        ]);
                    }
                }
            } else {
                $prodis = UserAccessFile::where('user_id', $user->id)->pluck('prodi_id')->toArray();
                $prodis_baru = array_map('intval', $request->prodi_id_auditor);
                if (count($prodis) == count($prodis_baru)) {
                    for ($i = 0; $i < count($prodis); $i++) {
                        if ($prodis_baru[$i] && $prodis[$i] != $prodis_baru[$i]) {
                            $access[$i]->update([
                                'prodi_id' => $prodis_baru[$i]
                            ]);
                        } elseif ($prodis_baru[$i] == 0) {
                            $access[$i]->delete();
                        }
                    }
                } elseif (count($prodis) < count($prodis_baru)) {
                    UserAccessFile::where('user_id', $user->id)->delete();
                    foreach ($prodis_baru as $prodi) {
                        if ($prodi) {
                            UserAccessFile::create([
                                'user_id' => $request->id,
                                'prodi_id' => $prodi,
                                'jurusan_id' => null
                            ]);
                        }
                    }
                }
            }
        } elseif ($request->role_id == 2) {
            if ($user->role_id == 3 || $user->role_id == 2) {
                $access[0]->update([
                    'jurusan_id' => $request->jurusan_id,
                    'prodi_id' => null
                ]);
            } elseif ($user->role_id == 4) {
                if ($access->count() > 1) {
                    $access->where('id', '!=', $access[0]->id)->each(function ($userAccessFile) {
                        $userAccessFile->delete();
                    });
                    $access[0]->update([
                        'prodi_id' => null,
                        'jurusan_id' => $request->jurusan_id
                    ]);
                }
            } else {
                UserAccessFile::create([
                    'user_id' => $request->id,
                    'prodi_id' => null,
                    'jurusan_id' => $request->jurusan_id
                ]);
            }
        } elseif ($request->role_id == 1) {
            UserAccessFile::where('user_id', $user->id)->delete();
        }

        if ($request->password) {
            if (Hash::check($request->password, $user->password)) {
                return back()->withErrors(['password' => 'Password baru tidak boleh sama dengan password lama.']);
            }

            $user->update([
                'role_id' => $request->role_id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
        } else {
            $user->update([
                'role_id' => $request->role_id,
                'name' => $request->name,
                'email' => $request->email,
            ]);
        }

        activity()
            ->performedOn($user)
            ->log('Mengubah data user dengan id ' . $user->id);

        return redirect('user')->with('success', 'Data user berhasil diubah');
    }
}