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
        'tahun',
        'status',
        'keterangan',
        'feedback',
        'tanggal_audit'
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }
}