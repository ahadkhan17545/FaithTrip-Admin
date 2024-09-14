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

    public static function convertIsoDurationToHoursAndMinutes($duration) {
        // Extract hours and minutes from the ISO 8601 duration
        preg_match('/PT(\d+)H(\d+)M/', $duration, $matches);

        // Extract hours and minutes
        $hours = $matches[1];
        $minutes = $matches[2];

        return array('hours' => $hours, 'minutes' => $minutes);
    }
}
