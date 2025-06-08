<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'project_id',
        'created_by',
        'due_date',
        'priority',
        'current_status',
        'start_date',
        'status',
        'is_repetitive',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'due_date' => 'datetime',
        'start_date' => 'datetime',
        'is_repetitive' => 'boolean',
    ];

    // Add status constants for better type safety
    const STATUS_PENDING_APPROVAL = 'pending_approval';
    const STATUS_APPROVED = 'approved';
    const STATUS_IN_PROGRESS = 'in_progress';

    const CURRENT_STATUS_TODO = 'todo';
    const CURRENT_STATUS_IN_PROGRESS = 'in_progress';
    const CURRENT_STATUS_COMPLETED = 'completed';

    // Add validation rules as a static property
    public static $rules = [
        'title' => 'required|string|max:100',
        'description' => 'nullable|string',
        'project_id' => 'required|exists:projects,id',
        'created_by' => 'required|exists:users,id',
        'due_date' => 'nullable|date',
        'priority' => 'required|in:low,medium,high',
        'current_status' => 'required|in:todo,in_progress,completed',
        'start_date' => 'nullable|date',
        'status' => 'required|in:pending_approval,approved,in_progress',
        'is_repetitive' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            if (!$task->status) {
                $task->status = self::STATUS_PENDING_APPROVAL;
            }
        });
    }

    public function repetitiveTask(): BelongsTo
    {
        return $this->belongsTo(RepetitiveTask::class, 'id', 'task_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function taskAssignments(): HasMany
    {
        return $this->hasMany(TaskAssignment::class);
    }
    
    public function taskComments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }
    
    public function statusHistory(): HasMany
    {
        return $this->hasMany(TaskStatusHistory::class);
    }
    
    /**
     * Get all reminders for this task
     */
    public function taskReminders(): HasMany
    {
        return $this->hasMany(TaskReminder::class);
    }
    
    /**
     * Get all assigned users for this task
     */
    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'task_assignments', 'task_id', 'user_id')
            ->withPivot('assigned_by', 'assigned_at')
            ->withTimestamps();
    }
}
