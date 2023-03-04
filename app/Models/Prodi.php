<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    use HasFactory;
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'jurusan_id',
        'nama_prodi',
    ];

    public function jurusan() {
        return $this->belongsTo(Jurusan::class);
    }

    public function user() {
        return $this->hasMany(User::class);
    }

    public function evaluasi_diri() {
        return $this->hasMany(EvaluasiDiri::class);
    }

    public function ketercapaian_standar() {
        return $this->hasMany(KetercapaianStandar::class);
    }
}
