<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\BelongsToTenant as TenantScope;

class ProductionBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id','project_id','bpom_registration_id','batch_number','quantity_produced','expiry_date','qc_status'
    ];

    protected $casts = [
        'expiry_date' => 'date:Y-m-d',
        'quantity_produced' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function bpom(): BelongsTo
    {
        return $this->belongsTo(BpomRegistration::class, 'bpom_registration_id');
    }

    public function qcResults(): HasMany
    {
        return $this->hasMany(QcResult::class, 'production_batch_id');
    }

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);
    }
}
