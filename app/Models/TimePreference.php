<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimePreference extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'key', 'start_time', 'end_time'];
}
