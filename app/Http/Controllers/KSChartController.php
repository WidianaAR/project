<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Jurusan;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;

class KSChartController extends Controller
{
    public function read_excel($data)
    {
        $param = [];
        $values = [];
        $value_param = [];

        for ($i = 0; $i < count($data); $i++) {
            $value = [];
            $file = IOFactory::load(storage_path('app/public/' . $data[$i]->file_data));
            $maxCell = $file->getSheetByName('Chart')->getHighestRowAndColumn();
            $sheetData = $file->getSheetByName('Chart')->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row']);
            for ($j = 0; $j < count($sheetData); $j++) {
                array_push($param, $sheetData[$j]['0']);
                array_push($value, intval($sheetData[$j]['1']));
            }
            array_push($values, $value);
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

        $value_param = collect($value_param)->map(function ($item) use ($data) {
            return $item / count($data);
        });
        $value_param = $value_param->toArray();

        return ['param' => $param, 'value' => $value_param];
    }

    public function home(Request $request)
    {
        $user = Auth::user();
        $jurusans = null;
        $prodis = null;
        $query = Dokumen::where(['kategori' => 'standar', 'status_id' => 7]);
        $keterangan = 'Tahun';
        $file_ed = Dokumen::where(['kategori' => 'evaluasi', 'tahun' => date('Y')]);
        $file_ks = Dokumen::where(['kategori' => 'standar', 'tahun' => date('Y')]);
        $file_tilik = Dokumen::where(['status_id' => 3, 'tahun' => date('Y')]);
        $file_kn = Dokumen::where(['status_id' => 6, 'tahun' => date('Y')]);
        $file_conf = Dokumen::where(['status_id' => 7, 'tahun' => date('Y')]);

        if ($user->role_id == 1) {
            $years = Dokumen::where(['kategori' => 'standar', 'status_id' => 7])->distinct()->pluck('tahun')->toArray();
            $prodis = Dokumen::where(['kategori' => 'standar', 'status_id' => 7])->with('prodi')->get()->groupBy('prodi.id')->map(function ($item) {
                return $item->unique('prodi.id');
            });
            $jurusans = Dokumen::where(['kategori' => 'standar', 'status_id' => 7])->with('prodi.jurusan')->get()->groupBy('prodi.jurusan.id')->map(function ($item) {
                return $item->unique('prodi.jurusan.id');
            });

            $file_ed = $file_ed->count();
            $file_ks = $file_ks->count();
            $file_tilik = $file_tilik->count();
            $file_kn = $file_kn->count();
            $file_conf = $file_conf->count();
        } elseif ($user->role_id == 2) {
            $query->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            });
            $years = Dokumen::where(['kategori' => 'standar', 'status_id' => 7])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            })->distinct()->pluck('tahun')->toArray();
            $prodis = Dokumen::where(['kategori' => 'standar', 'status_id' => 7])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            })->with('prodi')->get()->groupBy('prodi.id')->map(function ($item) {
                return $item->unique('prodi.id');
            });

            $file_ed = $file_ed->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            })->count();
            $file_ks = $file_ks->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            })->count();
            $file_tilik = $file_tilik->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            })->count();
            $file_kn = $file_kn->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            })->count();
            $file_conf = $file_conf->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            })->count();
        } elseif ($user->role_id == 3) {
            $query->where('prodi_id', $user->user_access_file[0]->prodi_id);
            [$file_ed, $file_ks, $file_tilik, $file_kn, $file_conf] = [null, null, null, null, null];
            $years = Dokumen::where(['kategori' => 'standar', 'status_id' => 7, 'prodi_id' => $user->user_access_file[0]->prodi_id])->distinct()->pluck('tahun')->toArray();
        } else {
            $auditor_prodis = [];
            foreach ($user->user_access_file as $value) {
                array_push($auditor_prodis, $value->prodi_id);
            }

            $query->whereIn('prodi_id', $auditor_prodis);
            $years = Dokumen::where(['kategori' => 'standar', 'status_id' => 7])->whereIn('prodi_id', $auditor_prodis)->distinct()->pluck('tahun')->toArray();
            $prodis = Dokumen::where(['kategori' => 'standar', 'status_id' => 7])->whereIn('prodi_id', $auditor_prodis)->with('prodi')->get()->groupBy('prodi.id')->map(function ($item) {
                return $item->unique('prodi.id');
            });

            $file_ed = $file_ed->whereIn('prodi_id', $auditor_prodis)->count();
            $file_ks = $file_ks->whereIn('prodi_id', $auditor_prodis)->count();
            $file_tilik = $file_tilik->whereIn('prodi_id', $auditor_prodis)->count();
            $file_kn = $file_kn->whereIn('prodi_id', $auditor_prodis)->count();
            $file_conf = $file_conf->whereIn('prodi_id', $auditor_prodis)->count();
        }

        if ($request->jurusan) {
            $query->withWhereHas('prodi.jurusan', function ($query) use ($request) {
                $query->where('id', $request->jurusan);
            });
            $keterangan = Jurusan::find($request->jurusan)->nama_jurusan;
        }
        if ($request->prodi) {
            $query->where('prodi_id', $request->prodi);
            $keterangan = Prodi::find($request->prodi)->nama_prodi;
        }
        if ($request->tahun) {
            $query->where('tahun', $request->tahun);
            $keterangan = $keterangan . ' ' . $request->tahun;
        }

        if ($request->all()) {
            $data = $query->get();
        } else {
            $data = $query->latest()->get()->take(1);
            $keterangan = ($data->count()) ? $data[0]->prodi->nama_prodi : 'Tahun';
        }

        $param_value = $this->read_excel($data);
        $param = $param_value['param'];
        $value = $param_value['value'];

        return view('dashboard.ks_chart', compact('years', 'jurusans', 'prodis', 'param', 'value', 'keterangan', 'file_ed', 'file_ks', 'file_tilik', 'file_kn', 'file_conf'));
    }
}