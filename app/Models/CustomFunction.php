<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFunction extends Model
{
    use HasFactory;

    public static function convertMinToHrMin($minutes) {
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($hours == 0) {
            return "{$remainingMinutes}min";
        } elseif ($remainingMinutes == 0) {
            return "{$hours}hr";
        } else {
            return "{$hours}hr {$remainingMinutes}min";
        }
    }
}
