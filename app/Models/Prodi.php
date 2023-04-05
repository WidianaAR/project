<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'kode_prodi',
        'jurusan_id',
        'nama_prodi',
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function user()
    {
        return $this->hasMany(User::class);
    }

    public function evaluasi_diri()
    {
        return $this->hasMany(EvaluasiDiri::class);
    }

    public function ketercapaian_standar()
    {
        return $this->hasMany(KetercapaianStandar::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    protected function namaProdi(): Attribute
    {
        return Attribute::make(
        set: fn(string $value) => ucwords($value),
        ); }
}