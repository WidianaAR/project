<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Prodi;
use Illuminate\Http\Request;

class ProdiController extends Controller
{
    public function index()
    {
        return view('prodi.home', ['prodis' => Prodi::with('jurusan')->get()]);
    }

    public function create()
    {
        return view('prodi.add_form', ['jurusans' => Jurusan::all()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_prodi' => 'required|unique:prodis',
            'jurusan_id' => 'required',
            'nama_prodi' => 'required|unique:prodis'
        ], [
                'kode_prodi.unique' => 'Kode prodi sudah terdaftar!',
                'nama_prodi.unique' => 'Nama prodi sudah terdaftar!'
            ]);
        Prodi::create($data);
        return redirect('prodis')->with('success', 'Data prodi berhasil ditambahkan');
    }

    // public function show($id) {}

    public function edit($id)
    {
        return view('prodi.change_form', [
            'data' => Prodi::find($id),
            'jurusans' => Jurusan::all()
        ]);
    }

    public function update(Request $request, Prodi $prodi)
    {
        $rules = [
            'kode_prodi' => 'required',
            'jurusan_id' => 'required',
            'nama_prodi' => 'required'
        ];

        if ($request->kode_prodi != $prodi->kode_prodi) {
            $rules['kode_prodi'] = 'required|unique:prodis';
        } elseif ($request->nama_prodi != $prodi->nama_prodi) {
            $rules['nama_prodi'] = 'required|unique:prodis';
        }

        $data = $request->validate($rules, [
            'kode_prodi.unique' => 'Kode prodi sudah terdaftar!',
            'nama_prodi.unique' => 'Nama prodi sudah terdaftar!'
        ]);

        Prodi::find($prodi->id)->update($data);
        return redirect('prodis')->with('success', 'Data prodi berhasil diubah');
    }

    public function destroy($id)
    {
        $data = Prodi::find($id);
        if ($data->user()->exists() || $data->evaluasi_diri()->exists() || $data->ketercapaian_standar()->exists()) {
            return back()->with('error', 'Data prodi tidak dapat dihapus karena masih memiliki data lain yang terkait.');
        }
        return back()->with('success', 'Data prodi berhasil dihapus');
    }
}