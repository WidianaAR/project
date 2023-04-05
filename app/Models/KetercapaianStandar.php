<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KetercapaianStandar extends Model
{
    use HasFactory;

    protected $fillable = [
        'jurusan_id',
        'prodi_id',
        'file_data',
        'tahun',
        'status',
        'keterangan'
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }
}