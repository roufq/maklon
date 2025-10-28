<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToTenant as TenantScope;

class QualityCheckpoint extends Model
{
    protected $fillable = ['tenant_id','project_id','name','criteria','required'];

    protected $casts = [
        'criteria' => 'array',
        'required' => 'boolean',
    ];

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);
    }
}

