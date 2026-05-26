<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    
    protected $table = 'tasks';

    
    protected $fillable = [
        'name',
        'subtask_name',
        'subtask_date',
        'subtask_time',
        'subtask_priority'
    ];
}