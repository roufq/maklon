<?php

namespace App\Policies;

use App\Models\ResourceAllocation;
use App\Models\User;

class ResourceAllocationPolicy
{
    public function before(User $user)
    {
        if ($user->hasRole('Admin')) return true;
    }

    protected function canAccess(User $user, ResourceAllocation $ra): bool
    {
        if ($ra->user_id === $user->id) return true;
        if ($ra->project) {
            if ($ra->project->created_by === $user->id) return true;
            if ($ra->project->team && $ra->project->team->members()->where('users.id', $user->id)->exists()) return true;
            if (\App\Models\Stakeholder::where('project_id', $ra->project_id)->where('email', $user->email)->exists()) return true;
        }
        return false;
    }

    public function viewAny(User $user): bool { return true; }
    public function view(User $user, ResourceAllocation $ra): bool { return $this->canAccess($user, $ra); }
    public function create(User $user): bool { return $user->can('resources.create'); }
    public function update(User $user, ResourceAllocation $ra): bool { return $user->can('resources.edit') && $this->canAccess($user, $ra); }
    public function delete(User $user, ResourceAllocation $ra): bool { return $user->can('resources.delete') && $this->canAccess($user, $ra); }
}

