<?php

namespace App\Models\Concerns;

use App\Support\Tenancy\TenantManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class BelongsToTenant implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $tenantId = TenantManager::getTenantId();
        if ($tenantId) {
            $builder->where($model->getTable().'.tenant_id', $tenantId);
        }
    }

    public static function bootTenant(Model $model): void
    {
        $model::creating(function ($m) {
            $tenantId = TenantManager::getTenantId();
            if ($tenantId && empty($m->tenant_id)) {
                $m->tenant_id = $tenantId;
            }
        });
    }
}

