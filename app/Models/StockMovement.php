<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToTenant as TenantScope;

class StockMovement extends Model
{
    protected $fillable = ['tenant_id','inventory_item_id','type','qty','reference_type','reference_id','by_user_id'];

    protected $casts = [
        'qty' => 'integer',
    ];

    public function item(): BelongsTo { return $this->belongsTo(InventoryItem::class, 'inventory_item_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'by_user_id'); }

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);
    }
}

