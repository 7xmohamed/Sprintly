<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    /**
     * Get the users that have this as their primary department.
     */
    public function primaryEmployees(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all users assigned to this department through user_departments.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_departments')
                    ->withTimestamps();
    }

    /**
     * Get all department assignments.
     */
    public function userDepartments(): HasMany
    {
        return $this->hasMany(UserDepartment::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
} 