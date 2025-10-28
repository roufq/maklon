<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToTenant as TenantScope;

class InventoryItem extends Model
{
    protected $fillable = ['tenant_id','name','supplier_id','current_stock','min_stock','unit','unit_cost'];

    protected $casts = [
        'current_stock' => 'integer',
        'min_stock' => 'integer',
        'unit_cost' => 'decimal:2',
    ];

    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function movements(): HasMany { return $this->hasMany(StockMovement::class); }

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);
    }
}

