<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function before(User $user)
    {
        if ($user->hasRole('Admin')) return true;
    }

    protected function canAccess(User $user, Project $project): bool
    {
        if ($project->created_by === $user->id) return true;
        if ($project->team && $project->team->members()->where('users.id', $user->id)->exists()) return true;
        if ($project->tasks()->where('assigned_to', $user->id)->exists()) return true;
        return \App\Models\Stakeholder::where('project_id', $project->id)->where('email', $user->email)->exists();
    }

    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Project $project): bool { return $this->canAccess($user, $project); }
    public function create(User $user): bool { return $user->can('projects.create'); }
    public function update(User $user, Project $project): bool { return $user->can('projects.edit') && $this->canAccess($user, $project); }
    public function delete(User $user, Project $project): bool { return $user->can('projects.delete') && $this->canAccess($user, $project); }
}

