<?php

namespace App\Policies;

use App\Models\Stakeholder;
use App\Models\User;

class StakeholderPolicy
{
    public function before(User $user)
    {
        if ($user->hasRole('Admin')) return true;
    }

    protected function canAccess(User $user, Stakeholder $s): bool
    {
        if ($s->project) {
            if ($s->project->created_by === $user->id) return true;
            if ($s->project->team && $s->project->team->members()->where('users.id', $user->id)->exists()) return true;
            if (\App\Models\Stakeholder::where('project_id', $s->project_id)->where('email', $user->email)->exists()) return true;
        }
        return false;
    }

    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Stakeholder $s): bool { return $this->canAccess($user, $s); }
    public function create(User $user): bool { return $user->can('projects.edit'); }
    public function update(User $user, Stakeholder $s): bool { return $user->can('projects.edit') && $this->canAccess($user, $s); }
    public function delete(User $user, Stakeholder $s): bool { return $user->can('projects.edit') && $this->canAccess($user, $s); }
}

