<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\KSDeadline;
use App\Models\Tahap;
use App\Traits\CountdownTrait;
use Illuminate\Http\Request;

class KSDeadlineController extends Controller
{
    use CountdownTrait;

    public function set_time()
    {
        $deadline = $this->KSCountdown();
        return view('deadlines.ks_deadline', compact('deadline'));
    }

    public function set_time_action(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'time' => 'required',
        ]);
        $datetime = $request->date . ' ' . $request->time;
        $data = KSDeadline::updateOrCreate(
            ['id' => $request->id],
            ['batas_waktu' => $datetime, 'status' => 'on going']
        );
        activity()
            ->performedOn($data)
            ->log('Mengatur waktu pengisian ketercapaian standar');
        return redirect()->route('ks_home')->with('success', 'Set deadline pengisian ketercapaian standar berhasil');
    }

    public function set_time_action_end($id)
    {
        $data = KSDeadline::find($id);
        $files = Dokumen::where(['kategori' => 'standar', 'status_id' => 1])->get();

        foreach ($files as $file) {
            $file->update(['status_id' => 2]);

            Tahap::create([
                'dokumen_id' => $file->id,
                'status_id' => 2
            ]);
        }

        activity()
            ->causedByAnonymous()
            ->performedOn($data)
            ->log('Waktu pengisian ketercapaian standar selesai');
        $data->update(['status' => 'finish']);
        return redirect()->route('ks_home');
    }
}