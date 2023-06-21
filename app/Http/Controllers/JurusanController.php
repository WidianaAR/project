<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index()
    {
        return view('jurusan.home', ['jurusans' => Jurusan::orderBy('kode_jurusan', 'asc')->paginate(8)]);
    }

    public function create()
    {
        return view('jurusan.add_form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_jurusan' => 'unique:jurusans',
            'nama_jurusan' => 'unique:jurusans',
            'keterangan' => 'unique:jurusans'
        ], [
                'kode_jurusan.unique' => 'Kode jurusan sudah terdaftar!',
                'nama_jurusan.unique' => 'Singkatan jurusan sudah terdaftar!',
                'keterangan.unique' => 'Nama jurusan sudah terdaftar'
            ]);
        $jurusan = Jurusan::create($data);
        activity()
            ->performedOn($jurusan)
            ->event('Manajemen data jurusan')
            ->log('Menambahkan data jurusan ' . $jurusan->nama_jurusan);
        return redirect('jurusans')->with('success', 'Data jurusan berhasil ditambahkan');
    }

    public function edit($id)
    {
        return view('jurusan.change_form', ['data' => Jurusan::find($id)]);
    }

    public function update(Request $request, Jurusan $jurusan)
    {
        $rules = [];

        if ($request->kode_jurusan != $jurusan->kode_jurusan) {
            $rules['kode_jurusan'] = 'unique:jurusans';
        }
        if ($request->nama_jurusan != $jurusan->nama_jurusan) {
            $rules['nama_jurusan'] = 'unique:jurusans';
        }
        if ($request->keterangan != $jurusan->keterangan) {
            $rules['keterangan'] = 'unique:jurusans';
        }

        $data = $request->validate($rules, [
            'kode_jurusan.unique' => 'Kode jurusan sudah terdaftar!',
            'nama_jurusan.unique' => 'Singkatan jurusan sudah terdaftar!',
            'keterangan.unique' => 'Nama jurusan sudah terdaftar'
        ]);
        Jurusan::find($jurusan->id)->update($data);
        activity()
            ->performedOn($jurusan)
            ->event('Manajemen data jurusan')
            ->log('Mengubah data jurusan dengan id ' . $jurusan->id);
        return redirect('jurusans')->with('success', 'Data jurusan berhasil diubah');
    }

    public function destroy($id)
    {
        $data = Jurusan::find($id);
        if ($data->prodi()->exists()) {
            return back()->with('error', 'Data jurusan tidak dapat dihapus karena masih memiliki data lain yang terkait!');
        }
        activity()
            ->performedOn($data)
            ->event('Manajemen data jurusan')
            ->log('Menghapus data jurusan ' . $data->nama_jurusan);
        $data->delete();
        return back()->with('success', 'Data jurusan berhasil dihapus');
    }
}