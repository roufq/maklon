<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant as TenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'leader_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    protected $attributes = [
        'type' => 'project_team',
    ];

    // Relationships
    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_user', 'team_id', 'user_id')
                    ->withTimestamps();
    }

    // Accessors
    public function getMemberCountAttribute()
    {
        return $this->members()->count();
    }

    public function getActiveProjectsCountAttribute()
    {
        return $this->projects()->where('status', 'active')->count();
    }

    // Scopes
    public function scopeProjectTeams($query)
    {
        return $query->where('type', 'project_team');
    }

    public function scopeDepartmentTeams($query)
    {
        return $query->where('type', 'department');
    }

    public function scopeByLeader($query, $leaderId)
    {
        return $query->where('leader_id', $leaderId);
    }

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);
    }
}
