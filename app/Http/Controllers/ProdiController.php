<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Prodi;
use Illuminate\Http\Request;

class ProdiController extends Controller
{
    public function index()
    {
        return view('prodi.home', ['prodis' => Prodi::with('jurusan')->orderBy('kode_prodi', 'asc')->paginate(8), 'jurusans' => Jurusan::all()]);
    }

    public function create()
    {
        return view('prodi.add_form', ['jurusans' => Jurusan::all()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_prodi' => 'unique:prodis',
            'nama_prodi' => 'unique:prodis'
        ], [
                'kode_prodi.unique' => 'Kode prodi sudah terdaftar!',
                'nama_prodi.unique' => 'Nama prodi sudah terdaftar!'
            ]);

        $prodi = Prodi::create($request->all());
        activity()
            ->performedOn($prodi)
            ->event('Manajemen data program studi')
            ->log('Menambah data prodi ' . $prodi->nama_prodi);
        return redirect('prodis')->with('success', 'Data prodi berhasil ditambahkan');
    }

    public function edit($id)
    {
        return view('prodi.change_form', [
            'data' => Prodi::find($id),
            'jurusans' => Jurusan::all()
        ]);
    }

    public function update(Request $request, Prodi $prodi)
    {
        $rules = [];

        if ($request->kode_prodi != $prodi->kode_prodi) {
            $rules['kode_prodi'] = 'unique:prodis';
        }
        if ($request->nama_prodi != $prodi->nama_prodi) {
            $rules['nama_prodi'] = 'unique:prodis';
        }

        $request->validate($rules, [
            'kode_prodi.unique' => 'Kode prodi sudah terdaftar!',
            'nama_prodi.unique' => 'Nama prodi sudah terdaftar!'
        ]);

        Prodi::find($prodi->id)->update($request->all());
        activity()
            ->performedOn($prodi)
            ->event('Manajemen data program studi')
            ->log('Mengubah data prodi dengan id ' . $prodi->id);
        return redirect('prodis')->with('success', 'Data prodi berhasil diubah');
    }

    public function destroy($id)
    {
        $data = Prodi::find($id);
        if ($data->dokumen()->exists()) {
            return back()->with('error', 'Data prodi tidak dapat dihapus karena masih memiliki data lain yang terkait!');
        }

        activity()
            ->performedOn($data)
            ->event('Manajemen data program studi')
            ->log('Menghapus data prodi ' . $data->nama_prodi);
        $data->delete();
        return back()->with('success', 'Data prodi berhasil dihapus');
    }

    public function prodi_filter($jurusan)
    {
        $jurusans = Jurusan::all();
        $prodis = Prodi::with('jurusan')->where('jurusan_id', $jurusan)->paginate(8);
        return view('prodi.home', compact('prodis', 'jurusans'));
    }
}