<?php

namespace App\Policies;

use App\Models\TimeEntry;
use App\Models\User;

class TimeEntryPolicy
{
    public function before(User $user)
    {
        if ($user->hasRole('Admin')) return true;
    }

    protected function canAccess(User $user, TimeEntry $entry): bool
    {
        if ($entry->user_id === $user->id) return true;
        if ($entry->project) {
            if ($entry->project->created_by === $user->id) return true;
            if ($entry->project->team && $entry->project->team->members()->where('users.id', $user->id)->exists()) return true;
            if (\App\Models\Stakeholder::where('project_id', $entry->project_id)->where('email', $user->email)->exists()) return true;
        }
        return false;
    }

    public function viewAny(User $user): bool { return true; }
    public function view(User $user, TimeEntry $entry): bool { return $this->canAccess($user, $entry); }
    public function create(User $user): bool { return $user->can('time.create'); }
    public function update(User $user, TimeEntry $entry): bool { return $user->can('time.edit') && $this->canAccess($user, $entry); }
    public function delete(User $user, TimeEntry $entry): bool { return $user->can('time.delete') && $this->canAccess($user, $entry); }
}

