<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description', 'tenant_id'])]
class Project extends Model
{
    use BelongsToTenant;

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
