<?php

namespace App\Http\Controllers;

use App\Models\KetercapaianStandar;
use Illuminate\Http\Request;
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
            $maxCell = $file->getSheet(5)->getHighestRowAndColumn();
            $sheetData = $file->getSheet(5)->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row']);
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

        return ['param' => $param, 'value' => $value_param];
    }

    public function home(Request $request)
    {
        $years = KetercapaianStandar::distinct()->pluck('tahun')->toArray();
        $prodis = KetercapaianStandar::where('status', 'disetujui')->join('prodis', 'prodis.id', '=', 'ketercapaian_standars.prodi_id')->select('prodis.nama_prodi', 'ketercapaian_standars.jurusan_id', 'ketercapaian_standars.prodi_id')->distinct()->get();
        $jurusans = KetercapaianStandar::where('status', 'disetujui')->select('jurusan_id')->distinct()->get();

        if (!!$request->all()) {
            if ($request->jurusan == 'all') {
                $data = KetercapaianStandar::where(['tahun' => $request->tahun, 'status' => 'disetujui'])->get();
                $keterangan = 'Ketercapaian standar semua jurusan tahun ' . $request->tahun;
            } elseif ($request->jurusan != 'all') {
                if ($request->prodi == 'all') {
                    $data = KetercapaianStandar::where(['tahun' => $request->tahun, 'jurusan_id' => $request->jurusan, 'status' => 'disetujui'])->get();
                    $keterangan = 'Ketercapaian standar ' . $data[0]->jurusan->nama_jurusan . ' tahun ' . $request->tahun;
                } else {
                    $data = KetercapaianStandar::where(['tahun' => $request->tahun, 'prodi_id' => $request->prodi, 'status' => 'disetujui'])->get();
                    $keterangan = 'Ketercapaian standar program studi ' . $data[0]->prodi->nama_prodi . ' tahun ' . $request->tahun;
                }
            }
        } else {
            $data = KetercapaianStandar::where(['status' => 'disetujui'])->latest()->get();
            if (!!$data->count()) {
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