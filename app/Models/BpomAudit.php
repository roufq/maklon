<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BpomAudit extends Model
{
    protected $fillable = ['bpom_registration_id','user_id','action','meta'];

    protected $casts = [
        'meta' => 'array',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(BpomRegistration::class, 'bpom_registration_id');
    }
}

