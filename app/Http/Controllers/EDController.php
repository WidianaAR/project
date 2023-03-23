<?php

namespace App\Http\Controllers;

use App\Models\EDDeadline;
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
            $years = EvaluasiDiri::where('jurusan_id', $jurusanId)->distinct()->pluck('tahun')->toArray();
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

    public function set_time()
    {
        $deadline = $this->EDCountdown();
        return view('evaluasi_diri.set_batas_waktu', compact('deadline'));
    }

    public function set_time_action(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'time' => 'required',
        ]);
        $datetime = $request->date . ' ' . $request->time;
        EDDeadline::updateOrCreate(
            ['id' => $request->id],
            ['batas_waktu' => $datetime, 'status' => 'on going']
        );
        return redirect()->route('ed_home')->with('success', 'Set Deadline Pengisian Evaluasi Diri Berhasil');
    }

    public function set_time_action_end($id)
    {
        EDDeadline::find($id)->update(['status' => 'finish']);
        return redirect()->route('ed_home');
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
            $prodi = Prodi::where('id', $request->prodi)->first();
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
                return redirect()->route('ed_table', $data->id)->with('success', 'File Berhasil Diganti');
            }
            return redirect()->route('ed_home')->with('success', 'File Berhasil Ditambahkan');
        }
        return redirect()->route('ed_home')->with('error', 'File Gagal Ditambahkan');
    }

    public function table($id_evaluasi)
    {
        $data = EvaluasiDiri::where('id', $id_evaluasi)->first();
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
            $file = EvaluasiDiri::where('id', $id_evaluasi)->first();
            $this->DeleteFile($file->file_data);
            $file->delete();
        }
        return redirect()->route('ed_home');
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

            $data = EvaluasiDiri::where('id', $request->id_evaluasi)->first();
            $this->DeleteFile($data->file_data);
            $extension = $request->file('file')->extension();
            $prodi = Prodi::where('id', $request->prodi)->first();
            $path = $this->UploadFile($request->file('file'), "Evaluasi Diri_" . $prodi->nama_prodi . "_" . $request->tahun . "." . $extension);
            EvaluasiDiri::updateOrCreate(
                ['id' => $request->id_evaluasi],
                [
                    'prodi_id' => $request->prodi,
                    'file_data' => $path
                ]
            );
            return redirect()->route('ed_home')->with('success', 'File Berhasil Diubah');
        }
        return redirect()->route('ed_home')->with('error', 'File Gagal Diubah');
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
            ;
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
        $data = EvaluasiDiri::where('id', $id_evaluasi)->first();
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
        return redirect()->route('ed_home')->with('success', 'Data Evaluasi Diri Disetujui');
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
        return redirect()->route('ed_table', $request->id_evaluasi)->with('success', 'Feedback Berhasil Disimpan');
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