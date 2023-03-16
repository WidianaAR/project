<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\KetercapaianStandar;
use App\Models\KSDeadline;
use App\Models\Prodi;
use App\Traits\CountdownTrait;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class KSController extends Controller
{
    use CountdownTrait;
    use FileTrait;
    
    public function home() {
        $data = KetercapaianStandar::all();
        $deadline = $this->KSCountdown();
        $jurusans = Jurusan::all();
        $prodis = Prodi::all();
        $years = KetercapaianStandar::distinct()->pluck('tahun')->toArray();

        if (Auth::user()->role_id == 2) {
            $data = KetercapaianStandar::where('jurusan_id', Auth::user()->jurusan_id)->get();
            $jurusans = null;
            $prodis = Prodi::where('jurusan_id', Auth::user()->jurusan_id)->get();
            $years = KetercapaianStandar::where('jurusan_id', Auth::user()->jurusan_id)->distinct()->pluck('tahun')->toArray();
            return view('ketercapaian_standar.home', compact('deadline', 'data', 'years', 'prodis', 'jurusans'));
        } elseif (Auth::user()->role_id == 3) {
            $ketercapaian_standar = KetercapaianStandar::where('prodi_id', Auth::user()->prodi_id)->first();
            if (!! $ketercapaian_standar) {
                $id_ed = $ketercapaian_standar->id;
            } else {
                $id_standar = null;
                $headers = null;
                $sheetData = null;
                $sheetName = null;
                $years = null;
                $data = null;
                return view('ketercapaian_standar.table', compact('deadline', 'id_standar', 'sheetData', 'headers', 'sheetName', 'years', 'data'));
            }
            return redirect()->route('ks_table', $id_ed);
        };
        return view('ketercapaian_standar.home', compact('deadline', 'years', 'prodis', 'data', 'jurusans'));
    }
    
    public function set_time()
    {
        $deadline = $this->KSCountdown();
        return view('ketercapaian_standar.set_batas_waktu', compact('deadline'));
    }

    public function set_time_action(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'time' => 'required',
        ]);
        $datetime = $request->date .' '. $request->time;
        KSDeadline::updateOrCreate(
            ['id' => $request->id],
            ['batas_waktu' => $datetime, 'status' => 'on going']
        );
        return redirect()->route('ks_home')->with('success', 'Set Deadline Pengisian Ketercapaian Standar Berhasil');
    }

    public function set_time_action_end($id)
    {
        KSDeadline::find($id)->update(['status' => 'finish']);
        return redirect()->route('ks_home');
    }

    public function add_action(Request $request) {
        if ($request->hasFile('file')) {
            $data = KetercapaianStandar::where([['prodi_id', '=', $request->prodi], ['tahun', '=', $request->tahun]])->first();
            if (!! $data) {
                $this->DeleteFile($data->file_data);
            }
            $extension = $request->file('file')->extension();
            $prodi = Prodi::where('id', $request->prodi)->first();
            $path = $this->UploadFile($request->file('file'), "Ketercapaian Standar_".$prodi->nama_prodi."_".$request->tahun.".".$extension);
            KetercapaianStandar::updateOrCreate(
                ['prodi_id' => $request->prodi, 'tahun' => $request->tahun],
                ['jurusan_id' => $request->jurusan,
                'file_data' => $path,
                'status' => 'ditinjau',
                'keterangan' => null]
            );
            if (Auth::user()->role_id == 4) {
                return redirect()->route('ks_table', $data->id)->with('success', 'File Berhasil Diganti');
            }
            return redirect()->route('ks_home')->with('success', 'File Berhasil Ditambahkan');
        }
        return redirect()->route('ks_home')->with('error', 'File Gagal Ditambahkan');
    }

    public function delete($id_standar) {
        if (!! $id_standar) {
            $file = KetercapaianStandar::where('id', $id_standar)->first();
            $this->DeleteFile($file->file_data);
            $file->delete();
        }
        return redirect()->route('ks_home');
    }

    public function table($id_standar) 
    {
        $headers = array();
        $sheetData = array();

        $data = KetercapaianStandar::where('id', $id_standar)->first();
        $file = IOFactory::load(storage_path('app/public/' . $data->file_data));
        $sheetCount = $file->getSheetCount();
        $sheetName = $file->getSheetNames();
        for ($i = 0; $i < $sheetCount; $i++) {
            $sheet = $file->getSheet($i)->toArray(null, true, true, true);
            $header = array_shift($sheet);

            array_push($sheetData, $sheet);
            array_push($headers, $header);
        }
        $years = KetercapaianStandar::where('prodi_id', $data->prodi_id)->distinct()->pluck('tahun')->toArray();
        $deadline = $this->KSCountdown();
        return view('ketercapaian_standar.table', compact('deadline', 'id_standar', 'sheetData', 'headers', 'sheetName', 'years', 'data'));
    }

    public function filter_tahun($tahun)
    {
        $deadline = $this->KSCountdown();
        $jurusans = Jurusan::all();
        if (Auth::user()->role_id == 2) {
            $prodis = Prodi::where('id', Auth::user()->jurusan_id)->get();
            $years = KetercapaianStandar::where('jurusan_id', Auth::user()->jurusan_id)->distinct()->pluck('tahun')->toArray();
            $data = KetercapaianStandar::where([['jurusan_id', '=', Auth::user()->jurusan_id], ['tahun', '=', $tahun]])->get();
        } elseif (Auth::user()->role_id == 3) {
            $data = KetercapaianStandar::where([['prodi_id', '=', Auth::user()->prodi_id], ['tahun', '=', $tahun]])->first();
            return redirect()->route('ks_table', $data->id);
        } else {
            $data = KetercapaianStandar::where('tahun', $tahun)->get();;
            $prodis = Prodi::all();
            $years = KetercapaianStandar::distinct()->pluck('tahun')->toArray();

        }
        return view('ketercapaian_standar.home', compact('deadline', 'data', 'years', 'prodis', 'jurusans'));
    }

    public function filter_prodi($prodi_id)
    {
        $jurusans = Jurusan::all();
        $deadline = $this->KSCountdown();
        if (Auth::user()->role_id == 2) {
            $prodis = Prodi::where('jurusan_id', Auth::user()->jurusan_id)->get();
            $years = KetercapaianStandar::where('jurusan_id', Auth::user()->jurusan_id)->distinct()->pluck('tahun')->toArray();
            $data = KetercapaianStandar::where([['jurusan_id', '=', Auth::user()->jurusan_id], ['prodi_id', '=', $prodi_id]])->get();
        } else {
            $prodis = Prodi::all();
            $years = KetercapaianStandar::distinct()->pluck('tahun')->toArray();
            $data = KetercapaianStandar::where('prodi_id', $prodi_id)->get();
        }
        return view('ketercapaian_standar.home', compact('deadline', 'data', 'years', 'prodis', 'jurusans'));
    }

    public function filter_jurusan($jurusan_id)
    {
        $jurusans = Jurusan::all();
        $deadline = $this->KSCountdown();
        $prodis = Prodi::all();
        $years = KetercapaianStandar::distinct()->pluck('tahun')->toArray();
        $data = KetercapaianStandar::where('jurusan_id', $jurusan_id)->get();
        return view('ketercapaian_standar.home', compact('deadline', 'data', 'years', 'prodis', 'jurusans'));
    }

    public function add() {
        $deadline = $this->KSCountdown();
        $prodis = Prodi::where('jurusan_id', Auth::user()->jurusan_id)->get();
        return view('ketercapaian_standar.import_form', compact('deadline', 'prodis'));
    }

    public function change($id_standar) {
        $deadline = $this->KSCountdown();
        $prodis = Prodi::where('jurusan_id', Auth::user()->jurusan_id)->get();
        $data = KetercapaianStandar::where('id', $id_standar)->first();
        return view('ketercapaian_standar.change_form', compact('deadline', 'prodis', 'data'));
    }

    public function change_action(Request $request) {
        if ($request->hasFile('file')) {
            $data = KetercapaianStandar::where('id', $request->id_standar)->first();
            $this->DeleteFile($data->file_data);
            $extension = $request->file('file')->extension();
            $prodi = Prodi::where('id', $request->prodi)->first();
            $path = $this->UploadFile($request->file('file'), "Ketercapaian Standar_".$prodi->nama_prodi."_".$request->tahun.".".$extension);
            KetercapaianStandar::updateOrCreate(
                ['id' => $request->id_standar],
                ['prodi_id' => $request->prodi,
                'jurusan_id' => $request->jurusan,
                'file_data' => $path]
            );
            return redirect()->route('ks_home')->with('success', 'File Berhasil Diubah');
        }
        return redirect()->route('ks_home')->with('error', 'File Gagal Diubah');
    }

    public function export_all(Request $request) {
        $zipname = 'Files/Ketercapaian Standar.zip';
        if (Storage::disk('public')->exists($zipname)) {
            $this->DeleteZip($zipname);
            $this->ExportZip($zipname, $request->data);
        } else {
            $this->ExportZip($zipname, $request->data);
        }
        return response()->download(storage_path('app/public/'.$zipname));
    }

    public function export_file(Request $request) {
        return response()->download(storage_path('app/public/'.$request->filename));
    }

    public function confirm($id_standar)
    {
        KetercapaianStandar::find($id_standar)->update([
            'status' => 'disetujui',
            'keterangan' => null,
        ]);
        return redirect()->route('ks_home')->with('success', 'Data Ketercapaian Standar Disetujui');
    }

    public function feedback(Request $request)
    {
        $request->validate([
            'id_standar' => 'required',
            'feedback' => 'required',
        ]);
        KetercapaianStandar::find($request->id_standar)->update([
            'keterangan' => $request->feedback,
            'status' => 'perlu perbaikan',
        ]);
        return redirect()->route('ks_table', $request->id_standar)->with('success', 'Feedback Berhasil Disimpan');
    }

    public function cancel_confirm($id_standar)
    {
        KetercapaianStandar::find($id_standar)->update([
            'status' => 'ditinjau',
            'keterangan' => null,
        ]);
        return redirect()->route('ks_home');
    }
}
