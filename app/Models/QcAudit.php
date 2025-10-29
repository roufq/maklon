<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant as TenantScope;

class QcAudit extends Model
{
    use HasFactory;

    protected $fillable = ['tenant_id','qc_result_id','quality_checkpoint_id','user_id','action','meta'];

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

