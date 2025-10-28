<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToTenant as TenantScope;

class QcResult extends Model
{
    protected $fillable = ['tenant_id','production_batch_id','quality_checkpoint_id','status','notes','by_user_id'];

    public function batch(): BelongsTo { return $this->belongsTo(ProductionBatch::class, 'production_batch_id'); }
    public function checkpoint(): BelongsTo { return $this->belongsTo(QualityCheckpoint::class, 'quality_checkpoint_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'by_user_id'); }

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);
    }
}

