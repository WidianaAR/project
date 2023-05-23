<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;
    protected $fillable = [
        'prodi_id',
        'status_id',
        'kategori',
        'file_data',
        'tahun',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function tahap()
    {
        return $this->hasMany(Tahap::class);
    }
}