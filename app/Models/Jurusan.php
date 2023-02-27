<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;
    public $timestamps = false;
    
    protected $table = 'jurusan';
    protected $primaryKey = 'id_jurusan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_jurusan',
        'nama_jurusan',
        'keterangan',
    ];
}
