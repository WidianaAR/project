<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Deadline;
use App\Models\Tahap;
use App\Traits\CountdownTrait;
use Illuminate\Http\Request;

class EDDeadlineController extends Controller
{
    use CountdownTrait;

    public function set_time()
    {
        $deadline = $this->Countdown('evaluasi');
        $kategori = 'evaluasi';
        return view('deadlines.ed_deadline', compact('deadline', 'kategori'));
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
            ['kategori' => 'evaluasi', 'batas_waktu' => $datetime, 'status' => 'on going']
        );
        activity()
            ->performedOn($data)
            ->log('Mengatur waktu pengisian evaluasi diri');
        return redirect()->route('ed_home')->with('success', 'Set deadline pengisian evaluasi diri berhasil');
    }

    public function set_time_action_end($id)
    {
        $data = Deadline::find($id);
        $files = Dokumen::where(['kategori' => 'evaluasi', 'status_id' => 1])->get();

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
            ->log('Waktu pengisian evaluasi diri selesai');
        $data->update(['status' => 'finish']);
        return redirect()->route('ed_home');
    }
}