<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\Project::class => \App\Policies\ProjectPolicy::class,
        \App\Models\Task::class => \App\Policies\TaskPolicy::class,
        \App\Models\TimeEntry::class => \App\Policies\TimeEntryPolicy::class,
        \App\Models\ResourceAllocation::class => \App\Policies\ResourceAllocationPolicy::class,
        \App\Models\Risk::class => \App\Policies\RiskPolicy::class,
        \App\Models\Invoice::class => \App\Policies\InvoicePolicy::class,
        \App\Models\Stakeholder::class => \App\Policies\StakeholderPolicy::class,
        \App\Models\Comment::class => \App\Policies\CommentPolicy::class,
        \App\Models\Ticket::class => \App\Policies\TicketPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Grant all abilities to Admin role
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Admin') ? true : null;
        });
    }
}
