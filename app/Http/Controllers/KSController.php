<?php

namespace App\Http\Controllers;

use App\Models\KSDeadline;
use App\Traits\CountdownTrait;
use Illuminate\Http\Request;

class KSController extends Controller
{
    use CountdownTrait;
    
    public function home() {
        $deadline = $this->KSCountdown();
        return view('ketercapaian_standar.home', compact('deadline'));
    }
    
    public function set_waktu()
    {
        $deadline = $this->KSCountdown();
        return view('ketercapaian_standar.set_batas_waktu', compact('deadline'));
    }

    public function set_waktu_action(Request $request)
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

    public function set_waktu_action_end($id)
    {
        KSDeadline::find($id)->update(['status' => 'finish']);
        return redirect()->route('ks_home');
    }
}
