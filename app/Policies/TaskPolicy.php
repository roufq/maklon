<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function before(User $user)
    {
        if ($user->hasRole('Admin')) return true;
    }

    protected function canAccess(User $user, Task $task): bool
    {
        if ($task->assigned_to === $user->id || $task->created_by === $user->id) return true;
        if ($task->project) {
            // delegate to project policy-like checks quickly
            if ($task->project->created_by === $user->id) return true;
            if ($task->project->team && $task->project->team->members()->where('users.id', $user->id)->exists()) return true;
            if (\App\Models\Stakeholder::where('project_id', $task->project_id)->where('email', $user->email)->exists()) return true;
        }
        return false;
    }

    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Task $task): bool { return $this->canAccess($user, $task); }
    public function create(User $user): bool { return $user->can('tasks.create'); }
    public function update(User $user, Task $task): bool { return $user->can('tasks.edit') && $this->canAccess($user, $task); }
    public function delete(User $user, Task $task): bool { return $user->can('tasks.delete') && $this->canAccess($user, $task); }
}

