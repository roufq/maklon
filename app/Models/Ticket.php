<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant as TenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id','requested_by','assigned_to','title','status','priority',
    ];

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);

        static::creating(function (Ticket $ticket) {
            if (empty($ticket->project_sequence) && !empty($ticket->project_id)) {
                $next = (int) (Ticket::where('project_id', $ticket->project_id)->max('project_sequence') ?? 0) + 1;
                $ticket->project_sequence = $next;
            }
        });
    }

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function requester(): BelongsTo { return $this->belongsTo(User::class,'requested_by'); }
    public function assignee(): BelongsTo { return $this->belongsTo(User::class,'assigned_to'); }
    public function messages(): HasMany { return $this->hasMany(TicketMessage::class); }
}
