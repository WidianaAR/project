<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Panduan extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'judul',
        'keterangan',
        'file_data'
    ];

    public function Judul(): Attribute
    {
        return Attribute::make(
        set: fn(string $value) => ucwords($value)
        ); }
}