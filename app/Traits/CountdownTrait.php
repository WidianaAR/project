<?php

namespace App\Traits;

use App\Models\EDDeadline;
use App\Models\KSDeadline;

trait CountdownTrait
{
    public function EDCountdown() {
        $deadline = EDDeadline::where('status', 'on going')->first();
        if (!!$deadline) {
            $date = $deadline->batas_waktu;
            $id = $deadline->id;
        } else {
            $date = null;
            $id = null;
        };
        return [$date, $id];
    }

    public function KSCountdown() {
        $deadline = KSDeadline::where('status', 'on going')->first();
        if (!!$deadline) {
            $date = $deadline->batas_waktu;
            $id = $deadline->id;
        } else {
            $date = null;
            $id = null;
        };
        return [$date, $id];
    }
}