<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use App\Models\PengumumanUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengumumanController extends Controller
{
    public function store(Request $request)
    {
        $data = Pengumuman::create($request->all());
        activity()
            ->performedOn($data)
            ->log(Auth::user()->name . ' membuat pengumuman');
        return redirect()->back();
    }

    public function close(Request $request)
    {
        foreach ($request->pengumuman_id as $id) {
            PengumumanUser::create([
                'user_id' => Auth::user()->id,
                'pengumuman_id' => $id
            ]);
        }

        return redirect()->back();
    }
}