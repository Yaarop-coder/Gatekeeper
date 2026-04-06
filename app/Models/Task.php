<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'project_id',
        'title',
        'description',
        'status',
        'priority',
        'assigned_to',
        'is_completed',
        'due_at',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'is_completed' => 'boolean',
    ];

    // Status Constants
    public const STATUS_TODO = 'todo';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_REVIEW = 'review';

    public const STATUS_DONE = 'done';

    /**
     * Get a Tailwind-friendly color for each status badge
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_TODO => 'bg-slate-100 text-slate-600',
            self::STATUS_IN_PROGRESS => 'bg-blue-100 text-blue-600',
            self::STATUS_REVIEW => 'bg-amber-100 text-amber-600',
            self::STATUS_DONE => 'bg-green-100 text-green-600',
            default => 'bg-gray-100 text-gray-600',
        };
    }

    /**
     * Relationship to the Project
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relationship to Task Comments
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    /**
     * Relationship to the Tenant (SaaS owner)
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * The User assigned to the task (Primary Relationship)
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Alias for assignee to prevent "RelationNotFoundException"
     * when calling ->with('user') in controllers.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1).substr($words[1], 0, 1));
        }

        return strtoupper(substr($this->name, 0, 2));
    }
}
