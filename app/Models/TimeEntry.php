<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant as TenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'start_time',
        'end_time',
        'duration_minutes',
        'is_billable',
        'metadata',
        'user_id',
        'project_id',
        'task_id',
        'billable_rate',
        'billable_amount',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration_minutes' => 'integer',
        'is_billable' => 'boolean',
        'metadata' => 'array',
    ];

    protected $attributes = [
        'is_billable' => true,
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    // Accessors
    public function getDurationInHoursAttribute()
    {
        return ($this->duration ?? 0) / 60; // Convert minutes to hours
    }

    public function getIsRunningAttribute()
    {
        return $this->start_time && !$this->end_time;
    }

    // Attribute mapping for backward compatibility
    public function getDurationAttribute()
    {
        return $this->attributes['duration_minutes'] ?? null;
    }

    public function setDurationAttribute($value): void
    {
        $this->attributes['duration_minutes'] = $value;
    }

    public function getBillableAttribute()
    {
        return (bool) ($this->attributes['is_billable'] ?? false);
    }

    public function setBillableAttribute($value): void
    {
        $this->attributes['is_billable'] = (bool) $value;
    }

    // Scopes
    public function scopeBillable($query)
    {
        return $query->where('is_billable', true);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('start_time', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('start_time', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    public function scopeRunning($query)
    {
        return $query->whereNotNull('start_time')->whereNull('end_time');
    }

    public function scopeAccessibleTo($query, ?User $user)
    {
        if (!$user) return $query->whereRaw('1=0');
        if ($user->hasRole('Admin')) return $query;

        return $query->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereIn('project_id', function ($sub) use ($user) {
                  $sub->select('id')->from('projects')
                      ->where('created_by', $user->id)
                      ->orWhereIn('team_id', function ($t) use ($user) {
                          $t->select('team_id')->from('team_user')->where('user_id', $user->id);
                      })
                      ->orWhereIn('id', function ($t2) use ($user) {
                          $t2->select('project_id')->from('stakeholders')->where('email', $user->email);
                      });
              });
        });
    }

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);
    }
}
