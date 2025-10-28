<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function before(User $user)
    {
        if ($user->hasRole('Admin')) return true;
    }

    protected function canAccess(User $user, Invoice $invoice): bool
    {
        if (!$invoice->project) return false;
        if ($invoice->project->created_by === $user->id) return true;
        if ($invoice->project->team && $invoice->project->team->members()->where('users.id', $user->id)->exists()) return true;
        if (\App\Models\Stakeholder::where('project_id', $invoice->project_id)->where('email', $user->email)->exists()) return true;
        return false;
    }

    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Invoice $invoice): bool { return $this->canAccess($user, $invoice); }
    public function create(User $user): bool { return $user->can('budgets.create'); }
    public function update(User $user, Invoice $invoice): bool { return $user->can('budgets.edit') && $this->canAccess($user, $invoice); }
    public function delete(User $user, Invoice $invoice): bool { return $user->can('budgets.edit') && $this->canAccess($user, $invoice); }
}

