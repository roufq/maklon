<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TicketController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Ticket::class);
        $tickets = Ticket::with(['project','requester','assignee'])
            ->orderByDesc('id')->paginate(15);
        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        $this->authorize('create', Ticket::class);
        $projects = Project::orderBy('name')->get(['id','name']);
        return view('tickets.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Ticket::class);
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'priority' => 'nullable|in:low,normal,high,urgent',
        ]);

        $hasActive = Ticket::where('project_id', $data['project_id'])
            ->whereIn('status', ['open','in_progress'])
            ->exists();

        $ticket = Ticket::create([
            'project_id' => $data['project_id'],
            'requested_by' => Auth::id(),
            'title' => $data['title'],
            'priority' => $data['priority'] ?? 'normal',
            'status' => $hasActive ? 'queued' : 'open',
        ]);

        // Optional notification to project owner (creator) about new ticket
        try {
            $ownerId = optional($ticket->project->creator)->id;
            if ($ownerId && $ownerId !== Auth::id()) {
                \App\Models\Notification::create([
                    'title' => 'New Ticket',
                    'message' => str($ticket->title)->limit(120),
                    'type' => 'ticket_created',
                    'user_id' => $ownerId,
                    'data' => ['ticket_id' => $ticket->id],
                    'notifiable_type' => \App\Models\Ticket::class,
                    'notifiable_id' => $ticket->id,
                ]);
            }
        } catch (\Throwable $e) { /* no-op */ }

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket created');
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);
        $ticket->load(['project','requester','assignee','messages.user']);
        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        $this->authorize('update', $ticket);
        $projects = Project::orderBy('name')->get(['id','name']);
        return view('tickets.edit', compact('ticket','projects'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'required|in:low,normal,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'nullable|in:open,in_progress,closed,queued',
        ]);
        $ticket->update($data);
        return redirect()->route('tickets.show', $ticket)->with('success','Ticket updated');
    }

    public function close(Ticket $ticket)
    {
        $this->authorize('close', $ticket);
        $ticket->update(['status' => 'closed']);

        // Activate next queued ticket for this project
        $next = Ticket::where('project_id', $ticket->project_id)
            ->where('status','queued')
            ->orderBy('id')
            ->first();
        if ($next) {
            $next->update(['status' => 'open']);
        }

        return back()->with('success','Ticket closed');
    }
}
