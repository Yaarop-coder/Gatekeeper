<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model; // Add this

class Activity extends Model
{
    protected $fillable = ['tenant_id', 'user_id', 'description'];

    // 1. GLOBAL SCOPE: Automatically filter by tenant_id
    protected static function booted()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }

    // 2. Relationship to User (Who did it?)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 3. Relationship to Tenant (Which company?)
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
