<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant as TenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'task_id',
        'allocated_hours',
        'start_date',
        'end_date',
        'allocation_type',
        'notes'
    ];

    protected $casts = [
        'allocated_hours' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'allocation_type' => 'string'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function getUtilizationPercentageAttribute(): float
    {
        // Calculate utilization based on allocated hours vs working hours
        $workingDays = $this->start_date->diffInDays($this->end_date) + 1;
        $workingHours = $workingDays * 8; // Assuming 8 hours per day

        return ($this->allocated_hours / $workingHours) * 100;
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function ($q2) use ($startDate, $endDate) {
                  $q2->where('start_date', '<=', $startDate)
                     ->where('end_date', '>=', $endDate);
              });
        });
    }

    public function scopeAccessibleTo($query, ?User $user)
    {
        if (!$user) return $query->whereRaw('1=0');
        if ($user->hasRole('Admin')) return $query;
        return $query->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereHas('project', function ($p) use ($user) { $p->accessibleTo($user); });
        });
    }

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);
    }
}
