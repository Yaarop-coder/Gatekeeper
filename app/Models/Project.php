<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use App\Traits\BelongsToTenant;

#[Fillable(['title', 'description', 'tenant_id'])]
class Project extends Model
{
    use BelongsToTenant;
}