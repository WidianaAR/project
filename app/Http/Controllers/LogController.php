<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $activities = [
            'Autentikasi',
            'Manajemen data jurusan',
            'Manajemen data program studi',
            'Manajemen data pengguna',
            'Manajemen data panduan',
            'Simulasi akreditasi',
            'Audit mutu internal'
        ];

        $query = Activity::with('causer');
        $perPage = request()->query('per_page', 5);

        if ($request->activity) {
            $query->where('event', $request->activity);
        }
        if ($request->user) {
            $query->withWhereHas('causer.role', function ($query) use ($request) {
                $query->where('id', $request->user);
            });
        }
        if ($request->start_date && $request->end_date) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$request->start_date, $request->end_date])->get();
        }

        $datas = $query->latest()->paginate($perPage)->withQueryString();
        return view('log', compact('datas', 'activities'));
    }
}