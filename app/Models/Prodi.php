<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    use HasFactory;
    public $timestamps = false;

    // protected $table = 'prodi';
    // protected $primaryKey = 'id_prodi';

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
}
