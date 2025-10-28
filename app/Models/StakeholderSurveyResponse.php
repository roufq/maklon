<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToTenant as TenantScope;

class StakeholderSurveyResponse extends Model
{
    protected $fillable = ['tenant_id','survey_id','stakeholder_id','answers','rating','submitted_at'];
    protected $casts = ['answers' => 'array','submitted_at' => 'datetime'];

    public function survey(): BelongsTo { return $this->belongsTo(StakeholderSurvey::class,'survey_id'); }
    public function stakeholder(): BelongsTo { return $this->belongsTo(Stakeholder::class); }

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);
    }
}

