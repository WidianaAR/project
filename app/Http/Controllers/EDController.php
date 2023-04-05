<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiDiri;
use App\Models\Jurusan;
use App\Models\Prodi;
use App\Traits\CountdownTrait;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EDController extends Controller
{
    use CountdownTrait;
    use FileTrait;

    public function home()
    {
        $data = EvaluasiDiri::with(['jurusan', 'prodi'])->get();
        $jurusans = Jurusan::all();
        $prodis = Prodi::all();
        $deadline = $this->EDCountdown();
        $years = EvaluasiDiri::distinct()->pluck('tahun')->toArray();

        if (Auth::user()->role_id == 2) {
            $jurusanId = Auth::user()->jurusan_id;
            $data = EvaluasiDiri::where('jurusan_id', $jurusanId)->get();
            $jurusans = null;
            $prodis = Prodi::where('jurusan_id', $jurusanId)->get();
            $years = EvaluasiDiri::where('jurusan_id', Auth::user()->jurusan_id)->distinct()->pluck('tahun')->toArray();
            return view('evaluasi_diri.home', compact('deadline', 'data', 'years', 'prodis', 'jurusans'));
        } elseif (Auth::user()->role_id == 3) {
            $evaluasi_diri = EvaluasiDiri::where('prodi_id', Auth::user()->prodi_id)->first();
            if (!!$evaluasi_diri) {
                $id_ed = $evaluasi_diri->id;
            } else {
                $id_evaluasi = null;
                $sheetData = null;
                $years = null;
                $data = null;
                return view('evaluasi_diri.table', compact('deadline', 'id_evaluasi', 'sheetData', 'years', 'data'));
            }
            return redirect()->route('ed_table', $id_ed);
        }
        ;
        return view('evaluasi_diri.home', compact('deadline', 'years', 'prodis', 'data', 'jurusans'));
    }

    public function add_action(Request $request)
    {
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'required|mimes:xlsx',
            ], [
                    'file.mimes' => 'File yang diunggah harus berupa file XLSX.',
                ]);

            $data = EvaluasiDiri::where([['prodi_id', '=', $request->prodi], ['tahun', '=', $request->tahun]])->first();
            if (!!$data) {
                $this->DeleteFile($data->file_data);
            }
            $extension = $request->file('file')->extension();
            $prodi = Prodi::find($request->prodi);
            $path = $this->UploadFile($request->file('file'), "Evaluasi Diri_" . $prodi->nama_prodi . "_" . $request->tahun . "." . $extension);
            EvaluasiDiri::updateOrCreate(
                ['prodi_id' => $request->prodi, 'tahun' => $request->tahun],
                [
                    'jurusan_id' => $request->jurusan,
                    'file_data' => $path,
                    'status' => 'ditinjau',
                    'keterangan' => null
                ]
            );
            if (Auth::user()->role_id == 4) {
                return redirect()->route('ed_table', $data->id)->with('success', 'File berhasil diganti');
            }
            return redirect()->route('ed_home')->with('success', 'File berhasil ditambahkan');
        }
        return redirect()->route('ed_home')->with('error', 'File gagal ditambahkan');
    }

    public function table($id_evaluasi)
    {
        $data = EvaluasiDiri::find($id_evaluasi);

        if (Auth::user()->role_id == 3 && $data->prodi_id != Auth::user()->prodi_id) {
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
        } elseif (Auth::user()->role_id == 2 && $data->jurusan_id != Auth::user()->jurusan_id) {
            return redirect()->route('login')->withErrors(['login_gagal' => 'Anda tidak memiliki akses!']);
        }

        $file = IOFactory::load(storage_path('app/public/' . $data->file_data));
        $maxCell = $file->getSheet(0)->getHighestRowAndColumn();
        $sheetData = $file->getSheet(0)->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row'] - 1);
        $years = EvaluasiDiri::where('prodi_id', $data->prodi_id)->distinct()->pluck('tahun')->toArray();
        $deadline = $this->EDCountdown();
        return view('evaluasi_diri.table', compact('deadline', 'id_evaluasi', 'sheetData', 'years', 'data'));
    }

    public function delete($id_evaluasi)
    {
        if (!!$id_evaluasi) {
            $file = EvaluasiDiri::find($id_evaluasi);
            $this->DeleteFile($file->file_data);
            $file->delete();
        }
        return redirect()->route('ed_home')->with('success', 'File berhasil dihapus');
    }

    // tetap harus pake walaupun fungsi add dah ada karena klo diubah berdasarkan id_prodi dan tahun kan bisa aja yang diubah malah id_prodinya jadi datanya malah ketambah bukan keubah
    public function change_action(Request $request)
    {
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'required|mimes:xlsx',
            ], [
                    'file.mimes' => 'File yang diunggah harus berupa file XLSX.',
                ]);

            $data = EvaluasiDiri::find($request->id_evaluasi);
            $this->DeleteFile($data->file_data);
            $extension = $request->file('file')->extension();
            $prodi = Prodi::find($request->prodi);
            $path = $this->UploadFile($request->file('file'), "Evaluasi Diri_" . $prodi->nama_prodi . "_" . $request->tahun . "." . $extension);
            EvaluasiDiri::updateOrCreate(
                ['id' => $request->id_evaluasi],
                [
                    'prodi_id' => $request->prodi,
                    'file_data' => $path
                ]
            );
            return redirect()->route('ed_home')->with('success', 'File berhasil diubah');
        }
        return redirect()->route('ed_home')->with('error', 'File gagal diubah');
    }

    public function filter_year($year)
    {
        $deadline = $this->EDCountdown();
        $jurusans = Jurusan::all();
        if (Auth::user()->role_id == 2) {
            $prodis = Prodi::where('jurusan_id', Auth::user()->jurusan_id)->get();
            $years = EvaluasiDiri::where('jurusan_id', Auth::user()->jurusan_id)->distinct()->pluck('tahun')->toArray();
            $data = EvaluasiDiri::where([['jurusan_id', '=', Auth::user()->jurusan_id], ['tahun', '=', $year]])->get();
        } elseif (Auth::user()->role_id == 3) {
            $data = EvaluasiDiri::where([['prodi_id', '=', Auth::user()->prodi_id], ['tahun', '=', $year]])->first();
            return redirect()->route('ed_table', $data->id);
        } else {
            $data = EvaluasiDiri::where('tahun', $year)->get();
            $prodis = Prodi::all();
            $years = EvaluasiDiri::distinct()->pluck('tahun')->toArray();

        }
        return view('evaluasi_diri.home', compact('deadline', 'data', 'years', 'prodis', 'jurusans'));
    }

    public function filter_prodi($prodi_id)
    {
        $jurusans = Jurusan::all();
        $deadline = $this->EDCountdown();
        if (Auth::user()->role_id == 2) {
            $prodis = Prodi::where('jurusan_id', Auth::user()->jurusan_id)->get();
            $years = EvaluasiDiri::where('jurusan_id', Auth::user()->jurusan_id)->distinct()->pluck('tahun')->toArray();
            $data = EvaluasiDiri::where([['jurusan_id', '=', Auth::user()->jurusan_id], ['prodi_id', '=', $prodi_id]])->get();
        } else {
            $prodis = Prodi::all();
            $years = EvaluasiDiri::distinct()->pluck('tahun')->toArray();
            // $data = EvaluasiDiri::join('prodi', 'prodi.prodi_id', '=', 'evaluasi_diri.prodi_id')->join('jurusan', 'jurusan.jurusan_id', '=', 'evaluasi_diri.jurusan_id')->where('evaluasi_diri.prodi_id', $prodi_id)->get();
            $data = EvaluasiDiri::where('prodi_id', $prodi_id)->get(); //dipersingkat (join dihilangkan) karena menggunakan eloquent relationship
        }
        return view('evaluasi_diri.home', compact('deadline', 'data', 'years', 'prodis', 'jurusans'));
    }

    public function filter_jurusan($jurusan_id)
    {
        $jurusans = Jurusan::all();
        $deadline = $this->EDCountdown();
        $prodis = Prodi::all();
        $years = EvaluasiDiri::distinct()->pluck('tahun')->toArray();
        $data = EvaluasiDiri::where('jurusan_id', $jurusan_id)->get();
        return view('evaluasi_diri.home', compact('deadline', 'data', 'years', 'prodis', 'jurusans'));
    }

    public function add()
    {
        $deadline = $this->EDCountdown();
        $prodis = Prodi::where('jurusan_id', Auth::user()->jurusan_id)->get();
        return view('evaluasi_diri.import_form', compact('deadline', 'prodis'));
    }

    public function change($id_evaluasi)
    {
        $deadline = $this->EDCountdown();
        $prodis = Prodi::where('jurusan_id', Auth::user()->jurusan_id)->get();
        $data = EvaluasiDiri::find($id_evaluasi);
        return view('evaluasi_diri.change_form', compact('deadline', 'prodis', 'data'));
    }

    public function export_all(Request $request)
    {
        if (!!$request->data) {
            $zipname = 'Files/Evaluasi Diri.zip';
            if (Storage::disk('public')->exists($zipname)) {
                $this->DeleteZip($zipname);
                $this->ExportZip($zipname, $request->data);
            } else {
                $this->ExportZip($zipname, $request->data);
            }
            return response()->download(storage_path('app/public/' . $zipname));
        }
        return back()->with('error', 'Tidak ada data yang dipilih');
    }

    public function export_file(Request $request)
    {
        return response()->download(storage_path('app/public/' . $request->filename));
    }

    public function confirm($id_evaluasi)
    {
        EvaluasiDiri::find($id_evaluasi)->update([
            'status' => 'disetujui',
            'keterangan' => null,
        ]);
        return redirect()->route('ed_home')->with('success', 'Data evaluasi diri disetujui');
    }

    public function feedback(Request $request)
    {
        $request->validate([
            'id_evaluasi' => 'required',
            'feedback' => 'required',
        ]);
        EvaluasiDiri::find($request->id_evaluasi)->update([
            'keterangan' => $request->feedback,
            'status' => 'perlu perbaikan',
        ]);
        return redirect()->route('ed_table', $request->id_evaluasi)->with('success', 'Feedback berhasil disimpan');
    }

    public function cancel_confirm($id_evaluasi)
    {
        EvaluasiDiri::find($id_evaluasi)->update([
            'status' => 'ditinjau',
            'keterangan' => null,
        ]);
        return redirect()->route('ed_home');
    }
}