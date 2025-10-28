<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToTenant as TenantScope;

class ProjectBaseline extends Model
{
    protected $fillable = ['project_id','name','baseline_date','description'];

    protected $casts = [
        'baseline_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function evmPoints(): HasMany
    {
        return $this->hasMany(EvmPoint::class, 'baseline_id');
    }

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);
    }
}
