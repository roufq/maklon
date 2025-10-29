<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Project::class, 'project');
    }
    public function index()
    {
        $projects = Project::with(['creator', 'team'])
                          ->accessibleTo(Auth::user())
                          ->latest()
                          ->paginate(10);

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $teams = Team::all();
        $tenantId = \App\Support\Tenancy\TenantManager::getTenantId();
        $clients = \App\Models\User::role('Client')
            ->when($tenantId, fn($q)=>$q->where('tenant_id',$tenantId))
            ->orderBy('name')->get(['id','name','email']);
        $bpoms = \App\Models\BpomRegistration::orderBy('product_name')->get(['id','product_name','registration_number','status']);
        return view('projects.create', compact('teams','clients','bpoms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:planning,active,on_hold,completed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'team_id' => 'nullable|exists:teams,id',
            // Maklon fields
            'customer_id' => 'nullable|exists:users,id',
            'order_quantity' => 'nullable|integer|min:0',
            'due_date' => 'nullable|date',
            'production_status' => 'nullable|in:draft,scheduled,in_progress,blocked,completed',
            'bpom_registration_id' => 'nullable|exists:bpom_registrations,id',
        ]);

        $validated['created_by'] = Auth::id();

        Project::create($validated);

        return redirect()->route('projects.index')
                        ->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        if ($project->tenant_id && \App\Support\Tenancy\TenantManager::getTenantId() && $project->tenant_id !== \App\Support\Tenancy\TenantManager::getTenantId()) {
            abort(404);
        }
        $project->load(['creator', 'team', 'tasks.assignedUser', 'timeEntries.user']);
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $teams = Team::all();
        $tenantId = \App\Support\Tenancy\TenantManager::getTenantId();
        $clients = \App\Models\User::role('Client')
            ->when($tenantId, fn($q)=>$q->where('tenant_id',$tenantId))
            ->orderBy('name')->get(['id','name','email']);
        $bpoms = \App\Models\BpomRegistration::orderBy('product_name')->get(['id','product_name','registration_number','status']);
        return view('projects.edit', compact('project', 'teams','clients','bpoms'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:planning,active,on_hold,completed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'team_id' => 'nullable|exists:teams,id',
            // Maklon fields
            'customer_id' => 'nullable|exists:users,id',
            'order_quantity' => 'nullable|integer|min:0',
            'due_date' => 'nullable|date',
            'production_status' => 'nullable|in:draft,scheduled,in_progress,blocked,completed',
            'bpom_registration_id' => 'nullable|exists:bpom_registrations,id',
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project)
                        ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
                        ->with('success', 'Project deleted successfully.');
    }
}
