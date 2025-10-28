<?php

namespace App\Http\Controllers;

use App\Models\Risk;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RiskController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(\App\Models\Risk::class, 'risk');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Risk::with(['project', 'owner', 'identifiedBy']);

        // Filter by project if specified
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by status if specified
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by risk level if specified
        if ($request->has('risk_level') && $request->risk_level) {
            $query->whereRaw("
                CASE
                    WHEN (CASE probability
                        WHEN 'very_low' THEN 1
                        WHEN 'low' THEN 2
                        WHEN 'medium' THEN 3
                        WHEN 'high' THEN 4
                        WHEN 'very_high' THEN 5
                        ELSE 1 END) *
                    (CASE impact
                        WHEN 'very_low' THEN 1
                        WHEN 'low' THEN 2
                        WHEN 'medium' THEN 3
                        WHEN 'high' THEN 4
                        WHEN 'very_high' THEN 5
                        ELSE 1 END) <= 4 THEN 'Low'
                    WHEN (CASE probability
                        WHEN 'very_low' THEN 1
                        WHEN 'low' THEN 2
                        WHEN 'medium' THEN 3
                        WHEN 'high' THEN 4
                        WHEN 'very_high' THEN 5
                        ELSE 1 END) *
                    (CASE impact
                        WHEN 'very_low' THEN 1
                        WHEN 'low' THEN 2
                        WHEN 'medium' THEN 3
                        WHEN 'high' THEN 4
                        WHEN 'very_high' THEN 5
                        ELSE 1 END) <= 9 THEN 'Medium'
                    WHEN (CASE probability
                        WHEN 'very_low' THEN 1
                        WHEN 'low' THEN 2
                        WHEN 'medium' THEN 3
                        WHEN 'high' THEN 4
                        WHEN 'very_high' THEN 5
                        ELSE 1 END) *
                    (CASE impact
                        WHEN 'very_low' THEN 1
                        WHEN 'low' THEN 2
                        WHEN 'medium' THEN 3
                        WHEN 'high' THEN 4
                        WHEN 'very_high' THEN 5
                        ELSE 1 END) <= 16 THEN 'High'
                    ELSE 'Very High'
                END = ?", [$request->risk_level]);
        }

        $risks = $query->orderBy('created_at', 'desc')->paginate(15);
        $projects = Project::all();

        return view('risks.index', compact('risks', 'projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $projects = Project::all();
        $users = User::all();
        $selectedProject = $request->has('project_id') ? Project::find($request->project_id) : null;

        return view('risks.create', compact('projects', 'users', 'selectedProject'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'probability' => 'required|in:very_low,low,medium,high,very_high',
            'impact' => 'required|in:very_low,low,medium,high,very_high',
            'status' => 'required|in:identified,assessed,mitigated,closed,occurred',
            'mitigation_plan' => 'nullable|string',
            'contingency_plan' => 'nullable|string',
            'due_date' => 'nullable|date',
            'project_id' => 'required|exists:projects,id',
            'owner_id' => 'required|exists:users,id',
        ]);

        $validated['identified_by'] = auth()->id();

        Risk::create($validated);

        return redirect()->route('risks.index')
            ->with('success', 'Risk has been created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Risk $risk): View
    {
        $risk->load(['project', 'owner', 'identifiedBy']);

        return view('risks.show', compact('risk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Risk $risk): View
    {
        $projects = Project::all();
        $users = User::all();

        return view('risks.edit', compact('risk', 'projects', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Risk $risk): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'probability' => 'required|in:very_low,low,medium,high,very_high',
            'impact' => 'required|in:very_low,low,medium,high,very_high',
            'status' => 'required|in:identified,assessed,mitigated,closed,occurred',
            'mitigation_plan' => 'nullable|string',
            'contingency_plan' => 'nullable|string',
            'due_date' => 'nullable|date',
            'project_id' => 'required|exists:projects,id',
            'owner_id' => 'required|exists:users,id',
        ]);

        $risk->update($validated);

        return redirect()->route('risks.index')
            ->with('success', 'Risk has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Risk $risk): RedirectResponse
    {
        $risk->delete();

        return redirect()->route('risks.index')
            ->with('success', 'Risk has been deleted successfully.');
    }
}
