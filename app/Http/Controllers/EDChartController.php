<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiDiri;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EDChartController extends Controller
{
    public function read_excel($data)
    {
        $param = [];
        $values = [];
        $value_param = [];
        $legend = [];

        for ($i = 0; $i < count($data); $i++) {
            $value = [];
            $file = IOFactory::load(storage_path('app/public/' . $data[$i]->file_data));
            $maxCell = $file->getSheet(2)->getHighestRowAndColumn();
            $legend = $file->getSheet(2)->rangeToArray('A1:' . 'A' . $maxCell['row'] - 1);
            $sheetData = $file->getSheet(2)->rangeToArray('B1:' . $maxCell['column'] . $maxCell['row'] - 1);
            for ($j = 0; $j < count($sheetData); $j++) {
                array_push($param, $sheetData[$j]['0']);
                array_push($value, intval($sheetData[$j]['1']));
            }
            array_push($values, $value);
        }

        // Menjumlahkan semua array (tiap row)
        foreach ($values as $row) {
            foreach ($row as $key => $val) {
                if (!isset($value_param[$key])) {
                    $value_param[$key] = $val;
                } else {
                    $value_param[$key] += $val;
                }
            }
        }

        // Membagi tiap row yang sudah dijumlahkan dengan banyak data untuk mencari rata-rata
        $value_param = collect($value_param)->map(function ($item) use ($data) {
            return $item / count($data);
        });
        $value_param = $value_param->toArray();
        $legend = array_reduce($legend, 'array_merge', []);
        $legend = array_values(array_filter($legend));

        return ['param' => $param, 'value' => $value_param, 'legend' => $legend];
    }

    public function home(Request $request)
    {
        $years = EvaluasiDiri::distinct()->pluck('tahun')->toArray();
        $prodis = EvaluasiDiri::where('status', 'disetujui')->join('prodis', 'prodis.id', '=', 'evaluasi_diris.prodi_id')->select('prodis.nama_prodi', 'evaluasi_diris.jurusan_id', 'evaluasi_diris.prodi_id')->distinct()->get();
        $jurusans = EvaluasiDiri::where('status', 'disetujui')->select('jurusan_id')->distinct()->get();

        if (!!$request->all() and !!$request->tahun) {
            if ($request->jurusan == 'all') {
                $data = EvaluasiDiri::where(['tahun' => $request->tahun, 'status' => 'disetujui'])->get();
                $keterangan = 'Evaluasi diri semua jurusan tahun ' . $request->tahun;
            } elseif ($request->jurusan != 'all') {
                if ($request->prodi == 'all') {
                    $data = EvaluasiDiri::where(['tahun' => $request->tahun, 'jurusan_id' => $request->jurusan, 'status' => 'disetujui'])->get();
                    $keterangan = 'Evaluasi diri ' . $data[0]->jurusan->nama_jurusan . ' tahun ' . $request->tahun;
                } else {
                    $data = EvaluasiDiri::where(['tahun' => $request->tahun, 'prodi_id' => $request->prodi, 'status' => 'disetujui'])->get();
                    $keterangan = 'Evaluasi diri program studi ' . $data[0]->prodi->nama_prodi . ' tahun ' . $request->tahun;
                }
            }
        } else {
            $data = EvaluasiDiri::where(['status' => 'disetujui'])->latest()->get();
            if (!!$data->count()) {
                $keterangan = 'Evaluasi diri program studi ' . $data[0]->prodi->nama_prodi . ' tahun ' . $data[0]->tahun;
            } else {
                $keterangan = 'Data kosong';
            }
        }

        $param_value = $this->read_excel($data);
        $param = $param_value['param'];
        $value = $param_value['value'];
        $legend = $param_value['legend'];

        return view('dashboard.ed_chart', compact('years', 'jurusans', 'prodis', 'param', 'value', 'keterangan', 'legend'));
    }
}