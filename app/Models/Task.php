<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use BelongsToTenant; // Now Tasks are automatically secured too!

    protected $fillable = [
        'title',
        'project_id',
        'tenant_id',
        'is_completed',
        'priority',
        'due_at',
        'assigned_to_id',
    ];

    protected $casts = [
        'due_at' => 'date',
        'is_completed' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }
}
