<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant as TenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'production_status',
        'priority',
        'start_date',
        'end_date',
        'due_date',
        'budget',
        'metadata',
        'created_by',
        'team_id',
        'customer_id',
        'order_quantity',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'metadata' => 'array',
        'due_date' => 'date',
        'order_quantity' => 'integer',
    ];

    protected $attributes = [
        'status' => 'planning',
        'priority' => 'medium',
    ];

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);
    }

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function bpomRegistration(): BelongsTo
    {
        return $this->belongsTo(BpomRegistration::class, 'bpom_registration_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    // Accessors
    public function getProgressAttribute()
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) return 0;

        $completedTasks = $this->tasks()->where('status', 'completed')->count();
        return round(($completedTasks / $totalTasks) * 100, 2);
    }

    public function getTotalTimeAttribute()
    {
        return (int) $this->timeEntries()->sum('duration_minutes');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeAccessibleTo($query, ?User $user)
    {
        if (!$user) return $query->whereRaw('1=0');
        if ($user->hasRole('Admin')) return $query;

        return $query->where(function ($q) use ($user) {
            // creator
            $q->where('created_by', $user->id)
              // member of the team assigned to project
              ->orWhereIn('team_id', function ($sub) use ($user) {
                  $sub->select('team_id')->from('team_user')->where('user_id', $user->id);
              })
              // has tasks in the project assigned to the user
              ->orWhereIn('id', function ($sub) use ($user) {
                  $sub->select('project_id')->from('tasks')->where('assigned_to', $user->id);
              })
              // stakeholder mapping by email (for client users)
              ->orWhereIn('id', function ($sub) use ($user) {
                  $sub->select('project_id')->from('stakeholders')->where('email', $user->email);
              });
        });
    }
}
