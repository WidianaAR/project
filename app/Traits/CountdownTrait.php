<?php

namespace App\Traits;

use App\Models\Deadline;

trait CountdownTrait
{
    public function Countdown($kategori)
    {
        $deadline = Deadline::where(['kategori' => $kategori, 'status' => 'on going'])->first();
        if ($deadline) {
            $date = $deadline->batas_waktu;
            $id = $deadline->id;
        } else {
            $date = null;
            $id = null;
        }
        ;
        return [$date, $id];
    }
}