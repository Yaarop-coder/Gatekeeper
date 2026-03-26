<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use App\Models\Scopes\TenantScope;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        static::addGlobalScope(new TenantScope);
        // 1. When creating a new record, automatically attach the tenant_id
        static::creating(function ($model) {
            if (session()->has('tenant_id')) {
                $model->tenant_id = session()->get('tenant_id');
            }
        });

        // 2. Whenever we "GET" data, automatically filter by tenant_id
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (session()->has('tenant_id')) {
                $builder->where('tenant_id', session()->get('tenant_id'));
            }
        });
    }
}