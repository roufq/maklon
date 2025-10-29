<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToTenant as TenantScope;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = ['tenant_id','inventory_item_id','type','qty','quantity','reason','reference_type','reference_id','by_user_id'];

    protected $casts = [
        'qty' => 'integer',
        'quantity' => 'integer',
    ];

    public function item(): BelongsTo { return $this->belongsTo(InventoryItem::class, 'inventory_item_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'by_user_id'); }

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($movement) {
            $item = $movement->item;
            if ($movement->type === 'out') {
                $item->current_stock -= $movement->qty;
            } elseif ($movement->type === 'in') {
                $item->current_stock += $movement->qty;
            }
            $item->save();
        });
    }
}
