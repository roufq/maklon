<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant as TenantScope;
use App\Models\InventoryItem;

class Bom extends Model
{
    protected $fillable = ['tenant_id','product_name','materials','version','total_cost'];

    protected $casts = [
        'materials' => 'array',
        'total_cost' => 'decimal:2',
    ];

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);

        static::saving(function (Bom $bom) {
            $materials = $bom->materials ?? [];
            $total = 0.0;
            if (is_array($materials)) {
                foreach ($materials as $m) {
                    $itemId = $m['item_id'] ?? null;
                    $qty = (float)($m['qty'] ?? 0);
                    if ($itemId && $qty > 0) {
                        $item = InventoryItem::find($itemId);
                        if ($item && $item->unit_cost !== null) {
                            $total += (float)$item->unit_cost * $qty;
                        }
                    }
                }
            }
            $bom->total_cost = round($total, 2);
        });
    }
}
