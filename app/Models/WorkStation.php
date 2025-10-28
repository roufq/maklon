<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant as TenantScope;

class WorkStation extends Model
{
    protected $fillable = ['tenant_id','name','capacity_per_hour','status'];

    protected $casts = [
        'capacity_per_hour' => 'integer',
    ];

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);
    }
}

