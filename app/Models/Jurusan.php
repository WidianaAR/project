<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode_jurusan',
        'nama_jurusan',
        'keterangan',
    ];

    public function prodi()
    {
        return $this->hasMany(Prodi::class);
    }

    public function user_access_file()
    {
        return $this->hasMany(UserAccessFile::class);
    }

    protected function namaJurusan(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => strtoupper($value)
        );
    }
    protected function keterangan(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => ucwords($value)
        );
    }
}