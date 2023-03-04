<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluasiDiri extends Model
{
    use HasFactory;

    protected $fillable = [
        'prodi_id',
        'file_data',
        'size',
        'tahun',
        'status',
        'keterangan'
    ];

    public function prodi() {
        return $this->belongsTo(Prodi::class);
    }

    public function jurusan() {
        return $this->hasOneThrough(Jurusan::class, Prodi::class);
    }
}
