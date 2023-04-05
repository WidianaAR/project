<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'feedbacks';

    protected $fillable = [
        'prodi_id',
        'tanggal_audit',
        'keterangan'
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }
}