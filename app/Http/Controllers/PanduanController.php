<?php

namespace App\Http\Controllers;

use App\Models\Panduan;
use App\Traits\FileTrait;
use Illuminate\Http\Request;

class PanduanController extends Controller
{
    use FileTrait;

    public function index()
    {
        return view('panduan.home_pjm', ['panduans' => Panduan::latest()->paginate(8)]);
    }

    public function create()
    {
        return view('panduan.add_form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'keterangan' => 'required'
        ]);

        $extension = $request->file('file_data')->extension();
        $filename = $request->judul . '.' . $extension;

        $path = $this->UploadFilePanduan($request->file('file_data'), $filename);

        $panduan = Panduan::create([
            'judul' => $request->judul,
            'keterangan' => $request->keterangan,
            'file_data' => $path
        ]);
        activity()
            ->performedOn($panduan)
            ->log('Menambahkan data panduan ' . $panduan->judul);
        return redirect()->route('panduans.index')->with('success', 'Data panduan berhasil disimpan');
    }

    public function show($id)
    {
        return view('panduan.detail', ['panduan' => Panduan::find($id)]);
    }

    public function edit($id)
    {
        return view('panduan.change_form', ['panduan' => Panduan::find($id)]);
    }

    public function update(Request $request, Panduan $panduan)
    {
        $request->validate([
            'judul' => 'required',
            'keterangan' => 'required'
        ]);

        $extension = $request->file('file_data')->extension();
        $filename = $request->judul . '.' . $extension;

        $this->DeleteFile($panduan->file_data);
        $path = $this->UploadFilePanduan($request->file('file_data'), $filename);

        Panduan::find($panduan->id)->update([
            'judul' => $request->judul,
            'keterangan' => $request->keterangan,
            'file_data' => $path
        ]);
        activity()
            ->performedOn($panduan)
            ->log('Mengubah data panduan dengan id ' . $panduan->id);
        return redirect()->route('panduans.index')->with('success', 'Data panduan berhasil diubah');
    }

    public function destroy($id)
    {
        $panduan = Panduan::find($id);
        activity()
            ->performedOn($panduan)
            ->log('Menghapus data panduan ' . $panduan->judul);
        $panduan->delete();
        return back()->with('success', 'Data panduan berhasil dihapus');
    }
}