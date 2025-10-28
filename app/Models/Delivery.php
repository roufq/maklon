<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToTenant as TenantScope;

class Delivery extends Model
{
    protected $fillable = [
        'tenant_id','project_id','production_batch_id','customer_id','status','shipping_provider','tracking_number','shipping_address','shipped_at','delivered_at','notes','public_token'
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function batch(): BelongsTo { return $this->belongsTo(ProductionBatch::class, 'production_batch_id'); }
    public function customer(): BelongsTo { return $this->belongsTo(User::class, 'customer_id'); }

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);
    }

    public function getTrackingUrlAttribute(): ?string
    {
        if (!$this->shipping_provider || !$this->tracking_number) return null;
        $map = config('logistics.providers');
        $key = strtoupper($this->shipping_provider);
        if (!isset($map[$key])) return null;
        return str_replace('{tracking}', urlencode($this->tracking_number), $map[$key]);
    }
}
