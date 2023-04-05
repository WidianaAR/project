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
        EDDeadline::updateOrCreate(
            ['id' => $request->id],
            ['batas_waktu' => $datetime, 'status' => 'on going']
        );
        return redirect()->route('ed_home')->with('success', 'Set deadline pengisian evaluasi diri berhasil');
    }

    public function set_time_action_end($id)
    {
        EDDeadline::find($id)->update(['status' => 'finish']);
        return redirect()->route('ed_home');
    }
}