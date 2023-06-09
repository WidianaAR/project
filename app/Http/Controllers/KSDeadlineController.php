<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Deadline;
use App\Models\Tahap;
use App\Traits\CountdownTrait;
use Illuminate\Http\Request;

class KSDeadlineController extends Controller
{
    use CountdownTrait;

    public function set_time()
    {
        $deadline = $this->Countdown('standar');
        $kategori = 'standar';
        return view('deadlines.ks_deadline', compact('deadline', 'kategori'));
    }

    public function set_time_action(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'time' => 'required',
        ]);
        $datetime = $request->date . ' ' . $request->time;
        $data = Deadline::updateOrCreate(
            ['id' => $request->id],
            ['kategori' => 'standar', 'batas_waktu' => $datetime, 'status' => 'on going']
        );
        activity()
            ->performedOn($data)
            ->event('Audit mutu internal')
            ->log('Mengatur waktu pengisian instrumen audit mutu internal');
        return redirect()->route('ks_home')->with('success', 'Set deadline pengisian instrumen audit mutu internal berhasil');
    }

    public function set_time_action_end($id)
    {
        $data = Deadline::find($id);
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
            ->event('Audit mutu internal')
            ->log('Waktu pengisian instrumen audit mutu internal selesai');
        $data->update(['status' => 'finish']);
        return redirect()->route('ks_home');
    }
}