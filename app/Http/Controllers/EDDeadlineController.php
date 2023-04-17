<?php

namespace App\Http\Controllers;

use App\Models\EDDeadline;
use App\Traits\CountdownTrait;
use Illuminate\Http\Request;

class EDDeadlineController extends Controller
{
    use CountdownTrait;

    public function set_time()
    {
        $deadline = $this->EDCountdown();
        return view('deadlines.ed_deadline', compact('deadline'));
    }

    public function set_time_action(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'time' => 'required',
        ]);
        $datetime = $request->date . ' ' . $request->time;
        $data = EDDeadline::updateOrCreate(
            ['id' => $request->id],
            ['batas_waktu' => $datetime, 'status' => 'on going']
        );
        activity()
            ->performedOn($data)
            ->log('Mengatur waktu pengisian evaluasi diri');
        return redirect()->route('ed_home')->with('success', 'Set deadline pengisian evaluasi diri berhasil');
    }

    public function set_time_action_end($id)
    {
        $data = EDDeadline::find($id);
        activity()
            ->causedByAnonymous()
            ->performedOn($data)
            ->log('Waktu pengisian ketercapaian standar selesai');
        $data->update(['status' => 'finish']);
        return redirect()->route('ed_home');
    }
}