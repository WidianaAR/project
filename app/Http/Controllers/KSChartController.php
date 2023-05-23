<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
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

        if ($user->role_id == 1) {
            $years = Dokumen::where(['kategori' => 'standar', 'status_id' => 7])->distinct()->pluck('tahun')->toArray();
            $prodis = Dokumen::where(['kategori' => 'standar', 'status_id' => 7])->with('prodi')->get()->groupBy('prodi.id')->map(function ($item) {
                return $item->unique('prodi.id');
            });
            $jurusans = Dokumen::where(['kategori' => 'standar', 'status_id' => 7])->with('prodi.jurusan')->get()->groupBy('prodi.jurusan.id')->map(function ($item) {
                return $item->unique('prodi.jurusan.id');
            });
        } elseif ($user->role_id == 2) {
            $years = Dokumen::where(['kategori' => 'standar', 'status_id' => 7])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            })->distinct()->pluck('tahun')->toArray();
            $prodis = Dokumen::where(['kategori' => 'standar', 'status_id' => 7])->withWhereHas('prodi.jurusan', function ($query) use ($user) {
                $query->where('id', $user->user_access_file[0]->jurusan_id);
            })->with('prodi')->get()->groupBy('prodi.id')->map(function ($item) {
                return $item->unique('prodi.id');
            });
        } elseif ($user->role_id == 3) {
            $years = Dokumen::where(['kategori' => 'standar', 'status_id' => 7, 'prodi_id' => $user->user_access_file[0]->prodi_id])->distinct()->pluck('tahun')->toArray();
        } else {
            $auditor_prodis = [];
            foreach ($user->user_access_file as $value) {
                array_push($auditor_prodis, $value->prodi_id);
            }

            $years = Dokumen::where(['kategori' => 'standar', 'status_id' => 7])->whereIn('prodi_id', $auditor_prodis)->distinct()->pluck('tahun')->toArray();
            $prodis = Dokumen::where(['kategori' => 'standar', 'status_id' => 7])->whereIn('prodi_id', $auditor_prodis)->with('prodi')->get()->groupBy('prodi.id')->map(function ($item) {
                return $item->unique('prodi.id');
            });
        }

        if ($request->all() and $request->tahun) {
            if ($request->jurusan == 'all') {
                $data = Dokumen::where(['kategori' => 'standar', 'tahun' => $request->tahun, 'status_id' => 7])->get();
                $keterangan = 'Ketercapaian standar semua jurusan tahun ' . $request->tahun;
            } elseif ($request->jurusan != 'all') {
                if ($request->prodi == 'all') {
                    $data = Dokumen::withWhereHas('prodi.jurusan', function ($query) use ($request) {
                        $query->where('id', $request->jurusan);
                    })->where(['kategori' => 'standar', 'tahun' => $request->tahun, 'status_id' => 7])->get();
                    if ($data->count()) {
                        $keterangan = 'Ketercapaian standar ' . $data[0]->prodi->jurusan->nama_jurusan . ' tahun ' . $request->tahun;
                    } elseif ($user->role_id == 2) {
                        $data = Dokumen::withWhereHas('prodi.jurusan', function ($query) use ($user) {
                            $query->where('id', $user->user_access_file[0]->jurusan_id);
                        })->where(['kategori' => 'standar', 'tahun' => $request->tahun, 'status_id' => 7])->get();
                        $keterangan = 'Ketercapaian standar ' . $data[0]->prodi->jurusan->nama_jurusan . ' tahun ' . $data[0]->tahun;
                    } else {
                        $keterangan = 'Data kosong';
                    }
                } else {
                    $data = Dokumen::where(['kategori' => 'standar', 'tahun' => $request->tahun, 'prodi_id' => $request->prodi, 'status_id' => 7])->get();
                    if ($data->count()) {
                        $keterangan = 'Ketercapaian standar program studi ' . $data[0]->prodi->nama_prodi . ' tahun ' . $request->tahun;
                    } elseif ($user->role_id == 3) {
                        $data = Dokumen::where(['kategori' => 'standar', 'tahun' => $request->tahun, 'prodi_id' => $user->user_access_file[0]->prodi_id, 'status_id' => 7])->get();
                        $keterangan = 'Ketercapaian standar program studi ' . $data[0]->prodi->nama_prodi . ' tahun ' . $data[0]->tahun;
                    } else {
                        $keterangan = 'Data kosong';
                    }
                }
            }
        } else {
            $data = Dokumen::where(['kategori' => 'standar', 'status_id' => 7])->latest()->get()->take(1);
            if ($data->count()) {
                $keterangan = 'Ketercapaian standar program studi ' . $data[0]->prodi->nama_prodi . ' tahun ' . $data[0]->tahun;
            } else {
                $keterangan = 'Data kosong';
            }
        }

        $param_value = $this->read_excel($data);
        $param = $param_value['param'];
        $value = $param_value['value'];

        return view('dashboard.ks_chart', compact('years', 'jurusans', 'prodis', 'param', 'value', 'keterangan'));
    }
}