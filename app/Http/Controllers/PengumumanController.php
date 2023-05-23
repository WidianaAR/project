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
        Pengumuman::create($request->all());
        return redirect()->back();
    }

    public function close($id)
    {
        PengumumanUser::create([
            'user_id' => Auth::user()->id,
            'pengumuman_id' => $id
        ]);

        return redirect()->back();
    }
}