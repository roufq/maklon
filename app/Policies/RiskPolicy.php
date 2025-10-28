<?php

namespace App\Policies;

use App\Models\Risk;
use App\Models\User;

class RiskPolicy
{
    public function before(User $user)
    {
        if ($user->hasRole('Admin')) return true;
    }

    protected function canAccess(User $user, Risk $risk): bool
    {
        if ($risk->owner_id === $user->id || $risk->identified_by === $user->id) return true;
        if ($risk->project) {
            if ($risk->project->created_by === $user->id) return true;
            if ($risk->project->team && $risk->project->team->members()->where('users.id', $user->id)->exists()) return true;
            if (\App\Models\Stakeholder::where('project_id', $risk->project_id)->where('email', $user->email)->exists()) return true;
        }
        return false;
    }

    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Risk $risk): bool { return $this->canAccess($user, $risk); }
    public function create(User $user): bool { return $user->can('risks.create'); }
    public function update(User $user, Risk $risk): bool { return $user->can('risks.edit') && $this->canAccess($user, $risk); }
    public function delete(User $user, Risk $risk): bool { return $user->can('risks.delete') && $this->canAccess($user, $risk); }
}

