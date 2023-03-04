<?php

namespace App\Http\Controllers;

use App\Models\EDDeadline;
use App\Traits\CountdownTrait;
use Illuminate\Http\Request;

class EDController extends Controller
{
    use CountdownTrait;
    
    public function home() {
        $deadline = $this->EDCountdown();
        return view('evaluasi_diri.home', compact('deadline'));
    }

    public function set_waktu()
    {
        $deadline = $this->EDCountdown();
        return view('evaluasi_diri.set_batas_waktu', compact('deadline'));
    }

    public function set_waktu_action(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'time' => 'required',
        ]);
        $datetime = $request->date .' '. $request->time;
        EDDeadline::updateOrCreate(
            ['id' => $request->id],
            ['batas_waktu' => $datetime, 'status' => 'on going']
        );
        return redirect()->route('ed_home')->with('success', 'Set Deadline Pengisian Evaluasi Diri Berhasil');
    }

    public function set_waktu_action_end($id)
    {
        EDDeadline::find($id)->update(['status' => 'finish']);
        return redirect()->route('ed_home');
    }
}
