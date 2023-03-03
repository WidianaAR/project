<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
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
        'nama_jurusan',
        'keterangan',
    ];

    public function prodi() {
        return $this->hasMany(Prodi::class);
    }

    public function user() {
        return $this->hasMany(User::class);
    }
}
