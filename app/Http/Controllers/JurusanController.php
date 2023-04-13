<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index()
    {
        return view('jurusan.home', ['jurusans' => Jurusan::all()]);
    }

    public function create()
    {
        return view('jurusan.add_form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_jurusan' => 'required|unique:jurusans',
            'nama_jurusan' => 'required|unique:jurusans',
            'keterangan' => 'required|unique:jurusans'
        ], [
                'kode_jurusan.unique' => 'Kode jurusan sudah terdaftar!',
                'nama_jurusan.unique' => 'Singkatan jurusan sudah terdaftar!',
                'keterangan.unique' => 'Nama jurusan sudah terdaftar'
            ]);
        Jurusan::create($data);
        return redirect('jurusans')->with('success', 'Data jurusan berhasil ditambahkan');
    }

    // public function show($id) {}

    public function edit($id)
    {
        return view('jurusan.change_form', ['data' => Jurusan::find($id)]);
    }

    public function update(Request $request, Jurusan $jurusan)
    {
        $rules = [
            'kode_jurusan' => 'required',
            'nama_jurusan' => 'required',
            'keterangan' => 'required'
        ];

        if ($request->kode_jurusan != $jurusan->kode_jurusan) {
            $rules['kode_jurusan'] = 'required|unique:jurusans';
        } elseif ($request->nama_jurusan != $jurusan->nama_jurusan) {
            $rules['nama_jurusan'] = 'required|unique:jurusans';
        } elseif ($request->keterangan != $jurusan->keterangan) {
            $rules['keterangan'] = 'required|unique:jurusans';
        }

        $data = $request->validate($rules, [
            'kode_jurusan.unique' => 'Kode jurusan sudah terdaftar!',
            'nama_jurusan.unique' => 'Singkatan jurusan sudah terdaftar!',
            'keterangan.unique' => 'Nama jurusan sudah terdaftar'
        ]);
        Jurusan::find($jurusan->id)->update($data);
        return redirect('jurusans')->with('success', 'Data jurusan berhasil diubah');
    }

    public function destroy($id)
    {
        $data = Jurusan::find($id);
        if ($data->prodi()->exists()) {
            return back()->with('error', 'Data jurusan tidak dapat dihapus karena masih memiliki data lain yang terkait.');
        }
        $data->delete();
        return back()->with('success', 'Data jurusan berhasil dihapus');
    }
}