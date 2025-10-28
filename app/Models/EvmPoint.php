<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToTenant as TenantScope;

class EvmPoint extends Model
{
    protected $fillable = ['project_id','baseline_id','as_of_date','pv','ev','ac'];

    protected $casts = [
        'as_of_date' => 'date',
        'pv' => 'decimal:2',
        'ev' => 'decimal:2',
        'ac' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function baseline(): BelongsTo
    {
        return $this->belongsTo(ProjectBaseline::class, 'baseline_id');
    }

    public function getSpiAttribute(): ?float
    {
        return $this->pv > 0 ? round($this->ev / $this->pv, 2) : null;
    }

    public function getCpiAttribute(): ?float
    {
        return $this->ac > 0 ? round($this->ev / $this->ac, 2) : null;
    }

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);
    }
}
