<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    use HasFactory;

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

    public function user_access_file()
    {
        return $this->hasMany(UserAccessFile::class);
    }

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class);
    }

    protected function namaProdi(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => ucwords($value),
        );
    }
}