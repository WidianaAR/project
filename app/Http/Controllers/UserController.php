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
        $users = User::with(['role', 'user_access_file', 'user_access_file.jurusan', 'user_access_file.prodi'])->latest('updated_at')->paginate(8);
        return view('user.home', compact('users'));
    }

    public function user_filter($role)
    {
        $users = User::where('role_id', $role)->latest('updated_at')->paginate(8);
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
            'email' => 'email:dns|unique:users',
            'password' => 'min:8|string',
            'confirm' => 'same:password',
        ], [
                'name.unique' => 'Nama user sudah terdaftar!',
                'email.unique' => 'Email user sudah terdaftar!',
                'password.min' => 'Password minimal 8 karakter!',
                'confirm.same' => 'Password tidak sama, mohon periksa kembali password Anda.'
            ]);

        if ($request->role_id == 2 && !$request->jurusan_id) {
            return back()->with('error', 'Mohon pilih jurusan untuk Ketua Jurusan!');
        } elseif ($request->role_id == 3 && !$request->jurusan_id) {
            return back()->with('error', 'Mohon pilih program studi untuk Koorprodi!');
        } elseif ($request->role_id == 4) {
            $notAllNull = array_filter($request->prodi_id_auditor, function ($value) {
                return $value != null;
            });
            if (count($notAllNull) == 0) {
                return back()->with('error', 'Mohon pilih minimal 1 program studi untuk Auditor!');
            }
        }

        $jurusan_id = ($request->role_id == 3) ? Prodi::find($request->prodi_id)->jurusan_id : $request->jurusan_id;
        $user = User::create([
            'role_id' => $request->role_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        if ($request->role_id == 4) {
            $prodis_auditor = array_filter($request->prodi_id_auditor, function ($value) {
                return $value != null;
            });
            $prodis_auditor = array_unique($prodis_auditor);
            foreach ($prodis_auditor as $prodi) {
                UserAccessFile::create([
                    'user_id' => $user->id,
                    'jurusan_id' => $jurusan_id,
                    'prodi_id' => $prodi
                ]);
            }
        } elseif ($request->role_id == 2 || $request->role_id == 3) {
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
        $rules = ['email' => 'email:dns'];

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
            if ($request->prodi_id) {
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
            } else {
                return back()->with('error', 'Mohon pilih program studi untuk Koorprodi!');
            }
        } elseif ($request->role_id == 4) {
            $prodis_baru = array_map('intval', $request->prodi_id_auditor);
            $notAllNull = array_filter($prodis_baru, function ($value) {
                return $value != 0;
            });
            $notAllNull = array_unique($notAllNull);

            if (count($notAllNull) != 0) {
                if ($user->role_id == 2 || $user->role_id == 3) {
                    $prodis_baru = array_values($notAllNull);
                    $access[0]->update([
                        'prodi_id' => $prodis_baru[0],
                        'jurusan_id' => null
                    ]);
                    if (count($prodis_baru) > 1) {
                        foreach ($prodis_baru as $prodi) {
                            if ($prodi != $access[0]->prodi_id) {
                                UserAccessFile::create([
                                    'user_id' => $request->id,
                                    'prodi_id' => $prodi,
                                    'jurusan_id' => null
                                ]);
                            }
                        }
                    }
                } elseif ($user->role_id == 1) {
                    foreach ($notAllNull as $prodi) {
                        UserAccessFile::create([
                            'user_id' => $request->id,
                            'prodi_id' => $prodi,
                            'jurusan_id' => null
                        ]);
                    }
                } else {
                    UserAccessFile::where('user_id', $user->id)->delete();
                    foreach ($notAllNull as $prodi) {
                        UserAccessFile::create([
                            'user_id' => $request->id,
                            'prodi_id' => $prodi,
                            'jurusan_id' => null
                        ]);
                    }
                }
            } else {
                return back()->with('error', 'Mohon pilih minimal 1 program studi untuk Auditor!');
            }
        } elseif ($request->role_id == 2) {
            if ($request->jurusan_id) {
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
            } else {
                return back()->with('error', 'Mohon pilih jurusan untuk Ketua Jurusan!');
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

        $user->touch();
        activity()
            ->performedOn($user)
            ->log('Mengubah data user dengan id ' . $user->id);

        return redirect('user')->with('success', 'Data user berhasil diubah');
    }
}