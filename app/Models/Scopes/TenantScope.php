<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
{
    // Use the config we just set in the middleware
    $tenantId = config('app.tenant_id');

    if ($tenantId) {
        $builder->where('tenant_id', $tenantId);
    }
}
}