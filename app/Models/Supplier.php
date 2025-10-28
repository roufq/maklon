<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant as TenantScope;

class Supplier extends Model
{
    protected $fillable = [
        'tenant_id','name','type','bpom_certified','contact_info','rating','performance_score'
    ];

    protected $casts = [
        'bpom_certified' => 'boolean',
        'contact_info' => 'array',
        'rating' => 'integer',
        'performance_score' => 'decimal:2',
    ];

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);
    }
}

