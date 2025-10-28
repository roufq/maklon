<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant as TenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'budget_category_id',
        'entered_by',
        'amount',
        'spent_at',
        'billable',
        'currency',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'billable' => 'boolean',
        'spent_at' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BudgetCategory::class, 'budget_category_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
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
