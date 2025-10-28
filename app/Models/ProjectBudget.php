<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant as TenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectBudget extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'total_budget',
        'spent_amount',
        'currency',
        'threshold_warning_percent',
        'threshold_critical_percent',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'total_budget' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'threshold_warning_percent' => 'decimal:2',
        'threshold_critical_percent' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(BudgetCategory::class);
    }

    public function getVarianceAttribute(): float
    {
        return (float) ($this->total_budget - $this->spent_amount);
    }

    public function getUtilizationPercentAttribute(): float
    {
        if ($this->total_budget <= 0) return 0.0;
        return (float) (($this->spent_amount / $this->total_budget) * 100);
    }

    public function scopeAccessibleTo($query, ?User $user)
    {
        if (!$user) return $query->whereRaw('1=0');
        if ($user->hasRole('Admin')) return $query;
        return $query->whereHas('project', function ($q) use ($user) {
            $q->accessibleTo($user);
        });
    }

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);
    }
}
