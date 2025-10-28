<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StakeholderComm extends Model
{
    protected $fillable = [
        'stakeholder_id','subject','notes','planned_at','sent_at'
    ];

    protected $casts = [
        'planned_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function stakeholder(): BelongsTo
    {
        return $this->belongsTo(Stakeholder::class);
    }
}

