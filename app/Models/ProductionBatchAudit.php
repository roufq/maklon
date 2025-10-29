<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant as TenantScope;

class ProductionBatchAudit extends Model
{
    use HasFactory;

    protected $fillable = ['tenant_id','production_batch_id','user_id','action','meta'];

    protected $casts = [
        'meta' => 'array',
    ];

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);
    }
}

