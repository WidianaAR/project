<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tahap extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function dokumen()
    {
        return $this->belongsTo(Dokumen::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}