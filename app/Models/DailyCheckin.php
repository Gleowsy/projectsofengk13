<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyCheckin extends Model
{
    protected $fillable = [
        'user_id', 'energy_level', 'focus_level',
        'mood', 'motivation', 'available_time', 'stress_level', 'date',
    ];
}
