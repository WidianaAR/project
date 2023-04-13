<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiDiri;
use App\Models\Jurusan;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackEDController extends Controller
{
    public function index()
    {
        $keterangan = 'Semua data';
        $user = Auth::user();

        if ($user->role_id == 2) {
            $data = EvaluasiDiri::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->jurusan_id);
            })->with('prodi')->get();
            $jurusans = null;
            $prodis = Prodi::where('jurusan_id', $user->jurusan_id)->get();
            $years = EvaluasiDiri::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->jurusan_id);
            })->distinct()->pluck('tahun')->toArray();
        } elseif ($user->role_id == 3) {
            $evaluasi_diri = EvaluasiDiri::where('prodi_id', $user->prodi_id)->first();
            if ($evaluasi_diri) {
                $id_ed = $evaluasi_diri->id;
            } else {
                [$id_evaluasi, $sheetData, $years, $data] = null;
                return view('feeedback.table', compact('id_evaluasi', 'sheetData', 'years', 'data'));
            }
            return redirect()->route('ed_table', $id_ed);
        } else {
            $data = EvaluasiDiri::with(['prodi.jurusan', 'prodi'])->get();
            $jurusans = Jurusan::all();
            $prodis = Prodi::all();
            $years = EvaluasiDiri::distinct()->pluck('tahun')->toArray();
        }
        return view('feeedback.home', compact('years', 'prodis', 'data', 'jurusans', 'keterangan'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'prodi_id' => 'required',
            'tanggal_audit' => 'required',
            'keterangan' => 'required'
        ]);

        if (EvaluasiDiri::where([['prodi_id', $request->prodi_id], ['tanggal_audit', $request->tanggal_audit]])->exists()) {
            return back()->with('error', 'Data sudah ada di dalam database');
        }

        EvaluasiDiri::create($data);
        return redirect()->route('feedbacks.index')->with('success', 'Data feedback berhasil disimpan.');
    }

    public function show($id)
    {
        return view('feedback.detail', ['feedback' => EvaluasiDiri::with('prodi.jurusan')->find($id)]);
    }

    public function update(Request $request, EvaluasiDiri $feedback)
    {
        $data = $request->validate([
            'prodi_id' => 'required',
            'tanggal_audit' => 'required',
            'keterangan' => 'required'
        ]);

        if ($request->prodi_id != $feedback->prodi_id || $request->tanggal_adit != $feedback->tanggal_audit) {
            if (EvaluasiDiri::where([['prodi_id', $request->prodi_id], ['tanggal_audit', $request->tanggal_audit]])->exists()) {
                return back()->with('error', 'Data sudah ada di dalam database');
            }
        }

        EvaluasiDiri::find($feedback->id)->update($data);
        return redirect()->route('feedbacks.index')->with('success', 'Data feedback berhasil diubah');
    }

    public function destroy($id)
    {
        EvaluasiDiri::destroy($id);
        return back()->with('success', 'Data feedback berhasil dihapus');
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
        return view('feedback.home', compact('deadline', 'data', 'years', 'prodis', 'jurusans'));
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
        return view('feedback.home', compact('deadline', 'data', 'years', 'prodis', 'jurusans'));
    }

    public function filter_jurusan($jurusan_id)
    {
        $jurusans = Jurusan::all();
        $deadline = $this->EDCountdown();
        $prodis = Prodi::all();
        $years = EvaluasiDiri::distinct()->pluck('tahun')->toArray();
        $data = EvaluasiDiri::where('jurusan_id', $jurusan_id)->get();
        return view('feedback.home', compact('deadline', 'data', 'years', 'prodis', 'jurusans'));
    }
}