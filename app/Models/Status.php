<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class);
    }

    public function tahap()
    {
        return $this->hasMany(Tahap::class);
    }
}