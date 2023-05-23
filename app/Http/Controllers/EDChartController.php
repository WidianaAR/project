<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Pengumuman;
use App\Models\PengumumanUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EDChartController extends Controller
{
    public function read_excel($data)
    {
        [$param, $values, $value_param, $legend] = [[], [], [], []];
        [$values2, $value_param2] = [[], []];

        for ($i = 0; $i < count($data); $i++) {
            [$value, $value2] = [[], []];
            $file = IOFactory::load(storage_path('app/public/' . $data[$i]->file_data));
            $maxCell = $file->getSheet(2)->getHighestRowAndColumn();
            $legend = $file->getSheet(2)->rangeToArray('A1:' . 'A' . $maxCell['row'] - 1);
            $sheetData = $file->getSheet(2)->rangeToArray('B1:' . $maxCell['column'] . $maxCell['row'] - 1);
            $sheetData2 = $file->getSheet(2)->rangeToArray('C1:' . $maxCell['column'] . $maxCell['row'] - 1);
            for ($j = 0; $j < count($sheetData); $j++) {
                array_push($param, $sheetData[$j]['0']);
                array_push($value, intval($sheetData[$j]['1']));
                array_push($value2, intval($sheetData2[$j]['1']));
            }
            array_push($values, $value);
            array_push($values2, $value2);
        }

        foreach ($values as $row) {
            foreach ($row as $key => $val) {
                if (!isset($value_param[$key])) {
                    $value_param[$key] = $val;
                } else {
                    $value_param[$key] += $val;
                }
            }
        }

        foreach ($values2 as $row) {
            foreach ($row as $key => $val) {
                if (!isset($value_param2[$key])) {
                    $value_param2[$key] = $val;
                } else {
                    $value_param2[$key] += $val;
                }
            }
        }

        $value_param = collect($value_param)->map(function ($item) use ($data) {
            return $item / count($data);
        });
        $value_param2 = collect($value_param2)->map(function ($item) use ($data) {
            return $item / count($data);
        });

        $value_param = $value_param->toArray();
        $value_param2 = $value_param2->toArray();
        $legend = array_reduce($legend, 'array_merge', []);
        $legend = array_values(array_filter($legend));

        return ['param' => $param, 'value' => $value_param, 'value2' => $value_param2, 'legend' => $legend];
    }

    public function home(Request $request)
    {
        $user = Auth::user();
        $jurusans = null;
        $prodis = null;

        if ($user->role_id == 1) {
            $years = Dokumen::where(['kategori' => 'evaluasi', 'status_id' => 7])->distinct()->pluck('tahun')->toArray();
            $prodis = Dokumen::where(['kategori' => 'evaluasi', 'status_id' => 7])->with('prodi')->get()->groupBy('prodi.id')->map(function ($item) {
                return $item->unique('prodi.id');
            });
            $jurusans = Dokumen::where(['kategori' => 'evaluasi', 'status_id' => 7])->with('prodi.jurusan')->get()->groupBy('prodi.jurusan.id')->map(function ($item) {
                return $item->unique('prodi.jurusan.id');
            });

            $file_ed = Dokumen::where(['kategori' => 'evaluasi', 'tahun' => date('Y')])->count();
            $file_ks = Dokumen::where(['kategori' => 'standar', 'tahun' => date('Y')])->count();
            $file_tilik = Dokumen::where(['status_id' => 3, 'tahun' => date('Y')])->count();
            $file_kn = Dokumen::where(['status_id' => 6, 'tahun' => date('Y')])->count();
            $file_conf = Dokumen::where(['status_id' => 7, 'tahun' => date('Y')])->count();
        } elseif ($user->role_id == 2) {
            $years = Dokumen::where(['kategori' => 'evaluasi', 'status_id' => 7])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            })->distinct()->pluck('tahun')->toArray();
            $prodis = Dokumen::where(['kategori' => 'evaluasi', 'status_id' => 7])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            })->with('prodi')->get()->groupBy('prodi.id')->map(function ($item) {
                return $item->unique('prodi.id');
            });

            $file_ed = Dokumen::where(['kategori' => 'evaluasi', 'tahun' => date('Y')])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            })->count();
            $file_ks = Dokumen::where(['kategori' => 'standar', 'tahun' => date('Y')])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            })->count();
            $file_tilik = Dokumen::where(['status_id' => 3, 'tahun' => date('Y')])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            })->count();
            $file_kn = Dokumen::where(['status_id' => 6, 'tahun' => date('Y')])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            })->count();
            $file_conf = Dokumen::where(['status_id' => 7, 'tahun' => date('Y')])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            })->count();
        } elseif ($user->role_id == 3) {
            [$file_ed, $file_ks, $file_tilik, $file_kn, $file_conf] = [null, null, null, null, null];
            $years = Dokumen::where(['kategori' => 'evaluasi', 'status_id' => 7, 'prodi_id' => $user->user_access_file[0]->prodi_id])->distinct()->pluck('tahun')->toArray();
        } else {
            $auditor_prodis = [];
            foreach ($user->user_access_file as $value) {
                array_push($auditor_prodis, $value->prodi_id);
            }

            $years = Dokumen::where(['kategori' => 'evaluasi', 'status_id' => 7])->whereIn('prodi_id', $auditor_prodis)->distinct()->pluck('tahun')->toArray();
            $prodis = Dokumen::where(['kategori' => 'evaluasi', 'status_id' => 7])->whereIn('prodi_id', $auditor_prodis)->with('prodi')->get()->groupBy('prodi.id')->map(function ($item) {
                return $item->unique('prodi.id');
            });

            $file_ed = Dokumen::where(['kategori' => 'evaluasi', 'tahun' => date('Y')])->whereIn('prodi_id', $auditor_prodis)->count();
            $file_ks = Dokumen::where(['kategori' => 'standar', 'tahun' => date('Y')])->whereIn('prodi_id', $auditor_prodis)->count();
            $file_tilik = Dokumen::where(['status_id' => 3, 'tahun' => date('Y')])->whereIn('prodi_id', $auditor_prodis)->count();
            $file_kn = Dokumen::where(['status_id' => 6, 'tahun' => date('Y')])->whereIn('prodi_id', $auditor_prodis)->count();
            $file_conf = Dokumen::where(['status_id' => 7, 'tahun' => date('Y')])->whereIn('prodi_id', $auditor_prodis)->count();
        }

        if ($request->all() and $request->tahun) {
            if ($request->jurusan == 'all') {
                $data = Dokumen::where(['kategori' => 'evaluasi', 'tahun' => $request->tahun, 'status_id' => 7])->get();
                $keterangan = 'Evaluasi diri semua jurusan tahun ' . $request->tahun;
            } elseif ($request->jurusan != 'all') {
                if ($request->prodi == 'all') {
                    $data = Dokumen::withWhereHas('prodi.jurusan', function ($query) use ($request) {
                        $query->where('id', $request->jurusan);
                    })->where(['kategori' => 'evaluasi', 'tahun' => $request->tahun, 'status_id' => 7])->get();
                    if ($data->count()) {
                        $keterangan = 'Evaluasi diri ' . $data[0]->prodi->jurusan->nama_jurusan . ' tahun ' . $request->tahun;
                    } elseif ($user->role_id == 2) {
                        $data = Dokumen::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                            $query->where('id', $user->user_access_file[0]->jurusan_id);
                        })->where(['kategori' => 'evaluasi', 'tahun' => $request->tahun, 'status_id' => 7])->get();
                        $keterangan = 'Evaluasi diri ' . $data[0]->prodi->jurusan->nama_jurusan . ' tahun ' . $data[0]->tahun;
                    } else {
                        $keterangan = 'Data kosong';
                    }
                } else {
                    $data = Dokumen::where(['kategori' => 'evaluasi', 'tahun' => $request->tahun, 'prodi_id' => $request->prodi, 'status_id' => 7])->get();
                    if ($data->count()) {
                        $keterangan = 'Evaluasi diri program studi ' . $data[0]->prodi->nama_prodi . ' tahun ' . $request->tahun;
                    } elseif ($user->role_id == 3) {
                        $data = Dokumen::where(['kategori' => 'evaluasi', 'tahun' => $request->tahun, 'prodi_id' => $user->user_access_file[0]->prodi_id, 'status_id' => 7])->get();
                        $keterangan = 'Evaluasi diri program studi ' . $data[0]->prodi->nama_prodi . ' tahun ' . $data[0]->tahun;
                    } else {
                        $keterangan = 'Data kosong';
                    }
                }
            }
        } else {
            if ($user->role_id == 2) {
                $data = Dokumen::where(['kategori' => 'evaluasi', 'status_id' => 7])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                    $query->where('id', $user->user_access_file[0]->jurusan_id);
                })->latest()->get()->take(1);
            } elseif ($user->role_id == 3) {
                $data = Dokumen::where(['kategori' => 'evaluasi', 'status_id' => 7, 'prodi_id' => $user->user_access_file[0]->prodi_id])->latest()->get()->take(1);
            } elseif ($user->role_id == 3) {
                $data = Dokumen::where(['kategori' => 'evaluasi', 'status_id' => 7])->whereIn('prodi_id', $auditor_prodis)->latest()->get()->take(1);
            } else {
                $data = Dokumen::where(['kategori' => 'evaluasi', 'status_id' => 7])->latest()->get()->take(1);
            }

            if ($data->count()) {
                $keterangan = 'Evaluasi diri program studi ' . $data[0]->prodi->nama_prodi . ' tahun ' . $data[0]->tahun;
            } else {
                $keterangan = 'Data kosong';
            }
        }

        $param_value = $this->read_excel($data);
        $param = $param_value['param'];
        $value = $param_value['value'];
        $value2 = $param_value['value2'];
        $legend = $param_value['legend'];

        $pengumuman = Pengumuman::latest()->first();
        $pengumuman_user = ($pengumuman) ? PengumumanUser::where(['user_id' => Auth::user()->id, 'pengumuman_id' => $pengumuman->id])->first() : [null];

        if ($pengumuman_user) {
            $pengumuman = null;
        }

        $file = ($user->role_id == 3) ? Dokumen::where(['kategori' => 'evaluasi', 'tahun' => date('Y'), 'prodi_id' => $user->user_access_file[0]->prodi_id])->first() : null;

        return view('dashboard.ed_chart', compact('years', 'jurusans', 'prodis', 'param', 'value', 'value2', 'keterangan', 'legend', 'pengumuman', 'file_ed', 'file_ks', 'file_tilik', 'file_kn', 'file_conf', 'file'));
    }
}