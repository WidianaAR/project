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

    public function index_others()
    {
        return view('panduan.home', ['panduans' => Panduan::latest()->paginate(8)]);
    }

    public function create()
    {
        return view('panduan.add_form');
    }

    public function store(Request $request)
    {
        if ($request->file('file_data')) {
            $extension = $request->file('file_data')->extension();
            $filename = $request->judul . '.' . $extension;
            $path = $this->UploadFilePanduan($request->file('file_data'), $filename);
        } else {
            $path = null;
        }

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
        if ($request->file('file_data')) {
            $extension = $request->file('file_data')->extension();
            $filename = $request->judul . '.' . $extension;
            if ($panduan->file_data) {
                $this->DeleteFile($panduan->file_data);
            }
            $path = $this->UploadFilePanduan($request->file('file_data'), $filename);
        } else {
            $extension = pathinfo($panduan->file_data, PATHINFO_EXTENSION);
            $path = 'Panduans/' . $request->judul . '.' . $extension;
            $this->ChangeFileName($panduan->file_data, $path);
        }

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
        if ($panduan->file_data) {
            $this->DeleteFile($panduan->file_data);
        }
        activity()
            ->performedOn($panduan)
            ->log('Menghapus data panduan ' . $panduan->judul);
        $panduan->delete();
        return back()->with('success', 'Data panduan berhasil dihapus');
    }

    public function download($id)
    {
        $data = Panduan::find($id)->file_data;
        activity()->log('Download file panduan ' . basename($data));
        return response()->download(storage_path('app/public/' . $data));
    }
}