<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class TicketMessageController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        // Require ticket reply permission
        abort_unless(Gate::allows('permission','tickets.reply'),403);

        $data = $request->validate([
            'message' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
        ]);

        $path = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('tickets', 'public');
        }

        $msg = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $data['message'] ?? null,
            'attachment_path' => $path,
        ]);

        // Optional notification to requester and assignee (if any)
        try {
            $usersToNotify = collect([$ticket->requested_by, $ticket->assigned_to])
                ->filter()->unique()->reject(fn($id) => $id === Auth::id());
            foreach ($usersToNotify as $uid) {
                \App\Models\Notification::create([
                    'title' => 'New Ticket Message',
                    'message' => str($msg->message ?? 'Attachment')->limit(120),
                    'type' => 'ticket_message',
                    'user_id' => $uid,
                    'data' => ['ticket_id' => $ticket->id],
                    'notifiable_type' => \App\Models\Ticket::class,
                    'notifiable_id' => $ticket->id,
                ]);
            }
        } catch (\Throwable $e) { /* no-op */ }

        return back()->with('success','Message posted');
    }
}
