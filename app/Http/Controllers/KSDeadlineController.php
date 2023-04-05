<?php

namespace App\Http\Controllers;

use App\Models\KSDeadline;
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
        KSDeadline::updateOrCreate(
            ['id' => $request->id],
            ['batas_waktu' => $datetime, 'status' => 'on going']
        );
        return redirect()->route('ks_home')->with('success', 'Set deadline pengisian ketercapaian standar berhasil');
    }

    public function set_time_action_end($id)
    {
        KSDeadline::find($id)->update(['status' => 'finish']);
        return redirect()->route('ks_home');
    }
}