<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant as TenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Risk extends Model
{
    protected $table = 'risk_register';

    protected $fillable = [
        'title',
        'description',
        'probability',
        'impact',
        'status',
        'mitigation_plan',
        'contingency_plan',
        'due_date',
        'project_id',
        'owner_id',
        'identified_by'
    ];

    protected $casts = [
        'due_date' => 'date',
        'probability' => 'string',
        'impact' => 'string',
        'status' => 'string'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function identifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'identified_by');
    }

    public function getRiskScoreAttribute(): int
    {
        $probabilityValues = [
            'very_low' => 1,
            'low' => 2,
            'medium' => 3,
            'high' => 4,
            'very_high' => 5
        ];

        $impactValues = [
            'very_low' => 1,
            'low' => 2,
            'medium' => 3,
            'high' => 4,
            'very_high' => 5
        ];

        $prob = $probabilityValues[$this->probability] ?? 1;
        $impact = $impactValues[$this->impact] ?? 1;

        return $prob * $impact;
    }

    public function getRiskLevelAttribute(): string
    {
        $score = $this->risk_score;

        if ($score <= 4) return 'Low';
        if ($score <= 9) return 'Medium';
        if ($score <= 16) return 'High';
        return 'Very High';
    }

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);
    }
}
