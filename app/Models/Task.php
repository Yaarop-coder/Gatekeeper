<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use BelongsToTenant; // Now Tasks are automatically secured too!

    protected $fillable = [
        'tenant_id',
        'project_id',
        'title',
        'priority',
        'attachment_path',
        'is_completed',
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
