<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Ticket;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Gate::allows('permission','messages.view'),403);
        $ticketId = $request->query('ticket_id');
        $projectId = $request->query('project_id');
        $context = null; $contextType = null; $contextId = null;

        if ($ticketId) { $context = Ticket::with('project')->find($ticketId); $contextType = Ticket::class; $contextId = $ticketId; }
        if (!$context && $projectId) { $context = Project::find($projectId); $contextType = Project::class; $contextId = $projectId; }

        $messages = collect();
        $composerEnabled = false;
        if ($context) {
            // visibility: only participants
            abort_unless($this->canAccessContext($context), 403);
            $messages = Message::where('context_type', $contextType)
                ->where('context_id', $contextId)
                ->with('user')
                ->orderBy('id')->get();
            $composerEnabled = true;
        }

        // Build dropdown options with lightweight visibility rules
        $user = Auth::user();
        $tickets = Ticket::with('project')
            ->where(function($q) use ($user){
                $q->where('requested_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
            })
            ->orderByDesc('id')
            ->limit(200)
            ->get();
        $projects = Project::accessibleTo($user)
            ->orderBy('name')
            ->limit(200)
            ->get(['id','name']);

        return view('messages.index', compact('messages','context','contextType','contextId','composerEnabled','tickets','projects'));
    }

    public function store(Request $request)
    {
        abort_unless(Gate::allows('permission','messages.create'),403);
        $data = $request->validate([
            'ticket_id' => 'nullable|exists:tickets,id',
            'project_id' => 'nullable|exists:projects,id',
            'body' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
        ]);
        abort_if(empty($data['ticket_id']) && empty($data['project_id']), 422, 'ticket_id or project_id is required');

        $context = null; $contextType = null; $contextId = null;
        if (!empty($data['ticket_id'])) { $context = Ticket::with('project')->findOrFail($data['ticket_id']); $contextType = Ticket::class; $contextId = (int)$data['ticket_id']; }
        else { $context = Project::findOrFail($data['project_id']); $contextType = Project::class; $contextId = (int)$data['project_id']; }

        abort_unless($this->canAccessContext($context), 403);

        $path = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('messages', 'public');
        }

        Message::create([
            'context_type' => $contextType,
            'context_id' => $contextId,
            'user_id' => Auth::id(),
            'body' => $data['body'] ?? null,
            'attachment_path' => $path,
        ]);

        return back()->with('success','Message sent');
    }

    private function canAccessContext($context): bool
    {
        $user = Auth::user();
        if ($user->hasRole('Admin')) return true;
        if ($context instanceof Ticket) {
            $allowed = ($context->requested_by === $user->id) || ($context->assigned_to === $user->id);
            // also allow project creator and team members
            $project = $context->project;
            if ($project) {
                if ($project->created_by === $user->id) return true;
                $inTeam = \DB::table('team_user')->where('user_id',$user->id)->where('team_id',$project->team_id)->exists();
                $allowed = $allowed || $inTeam;
            }
            return $allowed;
        }
        if ($context instanceof Project) {
            if ($context->created_by === $user->id) return true;
            return \DB::table('team_user')->where('user_id',$user->id)->where('team_id',$context->team_id)->exists();
        }
        return false;
    }
}
