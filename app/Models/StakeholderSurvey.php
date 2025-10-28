<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToTenant as TenantScope;

class StakeholderSurvey extends Model
{
    protected $fillable = ['tenant_id','project_id','created_by','title','questions'];
    protected $casts = ['questions' => 'array'];

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class,'created_by'); }
    public function responses(): HasMany { return $this->hasMany(StakeholderSurveyResponse::class, 'survey_id'); }

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);
    }
}

