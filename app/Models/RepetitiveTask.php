<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepetitiveTask extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'task_id',
        'project_id',
        'created_by',
        'repetition_rate',
        'recurrence_interval',
        'recurrence_days',
        'recurrence_month_day',
        'start_date',
        'end_date',
        'next_occurrence',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'task_id' => 'integer',
            'project_id' => 'integer',
            'created_by' => 'integer',
            'recurrence_interval' => 'datetime',
            'recurrence_days' => 'integer',
            'recurrence_month_day' => 'integer',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'next_occurrence' => 'datetime',
        ];
    }
}
