<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant as TenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectBox extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id','box_type_id','size','shape','mockup_path',
    ];

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);
    }

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function boxType(): BelongsTo { return $this->belongsTo(BoxType::class,'box_type_id'); }
}

