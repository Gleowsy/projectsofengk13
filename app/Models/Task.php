<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    protected $fillable = [
        'user_id',
        'name',
        'subtask_name',  'subtask_date',  'subtask_time',  'subtask_priority',  'subtask_done',
        'subtask2_name', 'subtask2_date', 'subtask2_time', 'subtask2_priority', 'subtask2_done',
        'subtask3_name', 'subtask3_date', 'subtask3_time', 'subtask3_priority', 'subtask3_done',
    ];

    protected $casts = [
        'subtask_date'  => 'date',
        'subtask2_date' => 'date',
        'subtask3_date' => 'date',
        'subtask_done'  => 'boolean',
        'subtask2_done' => 'boolean',
        'subtask3_done' => 'boolean',
    ];
}