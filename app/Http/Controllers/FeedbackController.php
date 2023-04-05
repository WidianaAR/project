<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Jurusan;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = [];
        $keterangan = 'Semua data';
        $years = Feedback::selectRaw('YEAR(tanggal_audit) as year')->distinct()->groupBy('year')->pluck('year');
        $jurusans = Feedback::with('prodi.jurusan')->get()->groupBy('prodi.jurusan.id')->map(function ($item) {
            return $item->unique('prodi.jurusan.id');
        });
        $prodis = Feedback::with('prodi')->get()->groupBy('prodi.id')->map(function ($item) {
            return $item->unique('prodi.id');
        });

        $datas = Feedback::with('prodi', 'prodi.jurusan')->get();
        foreach ($datas as $feedback) {
            array_push($feedbacks, $feedback);
        }
        return view('feedback.home_auditor', compact('keterangan', 'feedbacks', 'years', 'jurusans', 'prodis'));
    }

    public function create()
    {
        return view('feedback.add_form', ['prodis' => Prodi::all()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'prodi_id' => 'required',
            'tanggal_audit' => 'required',
            'keterangan' => 'required'
        ]);

        if (Feedback::where([['prodi_id', $request->prodi_id], ['tanggal_audit', $request->tanggal_audit]])->exists()) {
            return back()->with('error', 'Data sudah ada di dalam database');
        }

        Feedback::create($data);
        return redirect()->route('feedbacks.index')->with('success', 'Data feedback berhasil disimpan.');
    }

    public function show($id)
    {
        return view('feedback.detail', ['feedback' => Feedback::with('prodi.jurusan')->find($id)]);
    }

    public function edit($id)
    {
        return view('feedback.change_form', ['feedback' => Feedback::find($id), 'prodis' => Prodi::all()]);
    }

    public function update(Request $request, Feedback $feedback)
    {
        $data = $request->validate([
            'prodi_id' => 'required',
            'tanggal_audit' => 'required',
            'keterangan' => 'required'
        ]);

        if ($request->prodi_id != $feedback->prodi_id || $request->tanggal_adit != $feedback->tanggal_audit) {
            if (Feedback::where([['prodi_id', $request->prodi_id], ['tanggal_audit', $request->tanggal_audit]])->exists()) {
                return back()->with('error', 'Data sudah ada di dalam database');
            }
        }

        Feedback::find($feedback->id)->update($data);
        return redirect()->route('feedbacks.index')->with('success', 'Data feedback berhasil diubah');
    }

    public function destroy($id)
    {
        Feedback::destroy($id);
        return back()->with('success', 'Data feedback berhasil dihapus');
    }

    public function home()
    {
        $feedbacks = [];
        $keterangan = 'Semua data';
        $role = Auth::user()->role_id;

        if ($role == 3) {
            $datas = Feedback::where('prodi_id', Auth::user()->prodi_id)->with('prodi', 'prodi.jurusan')->get();
            foreach ($datas as $feedback) {
                array_push($feedbacks, $feedback);
            }
            return view('feedback.home', compact('feedbacks'));
        } elseif ($role == 2) {
            $years = Feedback::withWhereHas('prodi.jurusan', function ($query) {
                $query->where('id', Auth::user()->jurusan_id);
            })->selectRaw('YEAR(tanggal_audit) as year')->distinct()->groupBy('year')->pluck('year');
            $jurusans = [];
            $prodis = Feedback::withWhereHas('prodi.jurusan', function ($query) {
                $query->where('id', Auth::user()->jurusan_id);
            })->get()->groupBy('prodi.id')->map(function ($item) {
                return $item->unique('prodi.id');
            });

            $datas = Feedback::withWhereHas('prodi.jurusan', function ($query) {
                $query->where('id', Auth::user()->jurusan_id);
            })->with('prodi')->get();
        } else {
            $years = Feedback::selectRaw('YEAR(tanggal_audit) as year')->distinct()->groupBy('year')->pluck('year');
            $jurusans = Feedback::with('prodi.jurusan')->get()->groupBy('prodi.jurusan.id')->map(function ($item) {
                return $item->unique('prodi.jurusan.id');
            });
            $prodis = Feedback::with('prodi')->get()->groupBy('prodi.id')->map(function ($item) {
                return $item->unique('prodi.id');
            });

            $datas = Feedback::with('prodi', 'prodi.jurusan')->get();
        }

        foreach ($datas as $feedback) {
            array_push($feedbacks, $feedback);
        }
        return view('feedback.home', compact('keterangan', 'feedbacks', 'years', 'jurusans', 'prodis'));
    }

    public function filter(Request $request)
    {
        $feedbacks = [];
        $keterangan = null;

        if (Auth::user()->role_id == 2) {
            $years = Feedback::withWhereHas('prodi.jurusan', function ($query) use ($request) {
                $query->where('id', Auth::user()->jurusan_id);
            })->selectRaw('YEAR(tanggal_audit) as year')->distinct()->groupBy('year')->pluck('year');
            $jurusans = [];
            $prodis = Feedback::withWhereHas('prodi.jurusan', function ($query) use ($request) {
                $query->where('id', Auth::user()->jurusan_id);
            })->with('prodi')->get()->groupBy('prodi.id')->map(function ($item) {
                return $item->unique('prodi.id');
            });

            if ($request->tahun == 'all' && $request->jurusan == 'all' && $request->prodi == 'all') {
                return redirect()->route('feedback_home');
            } elseif ($request->tahun == 'all' && $request->prodi != 'all') {
                $datas = Feedback::withWhereHas('prodi.jurusan', function ($query) use ($request) {
                    $query->where('id', Auth::user()->jurusan_id);
                })->where('prodi_id', $request->prodi)->with('prodi')->get();
                $prodi = Prodi::find($request->prodi);
                $keterangan = $prodi->nama_prodi;
            } elseif ($request->tahun != 'all' && $request->prodi == 'all') {
                $datas = Feedback::whereRaw('YEAR(tanggal_audit) = ?', $request->tahun)->withWhereHas('prodi.jurusan', function ($query) use ($request) {
                    $query->where('id', Auth::user()->jurusan_id);
                })->with('prodi')->get();
                $keterangan = $request->tahun;
            } else {
                $datas = Feedback::whereRaw('YEAR(tanggal_audit) = ?', $request->tahun)->withWhereHas('prodi.jurusan', function ($query) use ($request) {
                    $query->where('id', Auth::user()->jurusan_id);
                })->where('prodi_id', $request->prodi)->with('prodi')->get();
                $prodi = Prodi::find($request->prodi);
                $keterangan = $prodi->nama_prodi . ' ' . $request->tahun;
            }
        } else {
            $years = Feedback::selectRaw('YEAR(tanggal_audit) as year')->distinct()->groupBy('year')->pluck('year');
            $jurusans = Feedback::with('prodi.jurusan')->get()->groupBy('prodi.jurusan.id')->map(function ($item) {
                return $item->unique('prodi.jurusan.id');
            });
            $prodis = Feedback::with('prodi')->get()->groupBy('prodi.id')->map(function ($item) {
                return $item->unique('prodi.id');
            });

            if ($request->tahun == 'all' && $request->jurusan == 'all' && $request->prodi == 'all') {
                $datas = Feedback::with('prodi', 'prodi.jurusan')->get();
                $keterangan = 'Semua data';
            } elseif ($request->tahun == 'all' && $request->jurusan != 'all' && $request->prodi == 'all') {
                $datas = Feedback::withWhereHas('prodi.jurusan', function ($query) use ($request) {
                    $query->where('id', $request->jurusan);
                })->with('prodi')->get();
                $jurusan = Jurusan::find($request->jurusan);
                $keterangan = $jurusan->nama_jurusan;
            } elseif ($request->tahun == 'all' && $request->jurusan != 'all' && $request->prodi != 'all') {
                $datas = Feedback::withWhereHas('prodi.jurusan', function ($query) use ($request) {
                    $query->where('id', $request->jurusan);
                })->withWhereHas('prodi', function ($query) use ($request) {
                    $query->where('id', $request->prodi);
                })->get();
                $jurusan = Jurusan::find($request->jurusan);
                $keterangan = $jurusan->nama_jurusan;
            } else {
                if ($request->jurusan == 'all') {
                    $datas = Feedback::whereRaw('YEAR(tanggal_audit) = ?', $request->tahun)->with('prodi', 'prodi.jurusan')->get();
                    $keterangan = $request->tahun;
                } elseif ($request->jurusan != 'all') {
                    if ($request->prodi == 'all') {
                        $datas = Feedback::whereRaw('YEAR(tanggal_audit) = ?', $request->tahun)->withWhereHas('prodi.jurusan', function ($query) use ($request) {
                            $query->where('id', $request->jurusan);
                        })->with('prodi')->get();
                        $jurusan = Jurusan::find($request->jurusan);
                        $keterangan = $jurusan->nama_jurusan . ' ' . $request->tahun;
                    } else {
                        $datas = Feedback::whereRaw('YEAR(tanggal_audit) = ?', $request->tahun)->withWhereHas('prodi', function ($query) use ($request) {
                            $query->where('id', $request->prodi);
                        })->with('prodi.jurusan')->get();
                        $prodi = Prodi::find($request->prodi);
                        $keterangan = $prodi->nama_prodi . ' ' . $request->tahun;
                    }
                }
            }
        }

        foreach ($datas as $feedback) {
            array_push($feedbacks, $feedback);
        }
        if (Auth::user()->role_id == 4) {
            return view('feedback.home_auditor', compact('keterangan', 'feedbacks', 'years', 'jurusans', 'prodis'));
        } else {
            return view('feedback.home', compact('keterangan', 'feedbacks', 'years', 'jurusans', 'prodis'));
        }
    }
}