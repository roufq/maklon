<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\BelongsToTenant as TenantScope;

class BpomRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id','product_name','registration_number','approval_date','expiry_date','status','document_path'
    ];

    protected $casts = [
        'approval_date' => 'date:Y-m-d',
        'expiry_date' => 'date:Y-m-d',
    ];

    public function batches(): HasMany
    {
        return $this->hasMany(ProductionBatch::class, 'bpom_registration_id');
    }

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);
    }
}
