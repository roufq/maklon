<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('tickets.view');
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return $user->can('tickets.view');
    }

    public function create(User $user): bool
    {
        return $user->can('tickets.create');
    }

    public function update(User $user, Ticket $ticket): bool
    {
        // Allow edit if user can create tickets (managerial) or is requester/assignee with reply rights
        if ($user->can('tickets.create')) return true;
        if ($user->can('tickets.reply')) {
            return $ticket->requested_by === $user->id || $ticket->assigned_to === $user->id;
        }
        return false;
    }

    public function close(User $user, Ticket $ticket): bool
    {
        return $user->can('tickets.close') || $this->update($user, $ticket);
    }
}

