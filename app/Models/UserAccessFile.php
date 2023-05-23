<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccessFile extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }
}