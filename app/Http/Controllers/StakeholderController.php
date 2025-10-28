<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Stakeholder;
use App\Models\StakeholderComm;
use Illuminate\Http\Request;

class StakeholderController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(\App\Models\Stakeholder::class, 'stakeholder');
    }
    public function index(Request $request)
    {
        $projectId = $request->get('project_id');
        $query = Stakeholder::with('project')->whereHas('project', function($q){ $q->accessibleTo(auth()->user()); });
        if ($projectId) { $query->where('project_id', $projectId); }
        $stakeholders = $query->paginate(15);
        $projects = Project::all();
        return view('stakeholders.index', compact('stakeholders','projects','projectId'));
    }

    public function create()
    {
        $projects = Project::all();
        return view('stakeholders.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'role' => 'nullable|string|max:255',
            'influence_level' => 'required|in:very_low,low,medium,high,very_high',
            'interest_level' => 'required|in:very_low,low,medium,high,very_high',
            'communication_plan' => 'nullable|string',
        ]);
        $stakeholder = Stakeholder::create($data);
        return redirect()->route('stakeholders.show', $stakeholder)->with('success','Stakeholder created');
    }

    public function show(Stakeholder $stakeholder)
    {
        $stakeholder->load('project','comms');
        return view('stakeholders.show', compact('stakeholder'));
    }

    public function edit(Stakeholder $stakeholder)
    {
        $projects = Project::all();
        return view('stakeholders.edit', compact('stakeholder','projects'));
    }

    public function update(Request $request, Stakeholder $stakeholder)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'role' => 'nullable|string|max:255',
            'influence_level' => 'required|in:very_low,low,medium,high,very_high',
            'interest_level' => 'required|in:very_low,low,medium,high,very_high',
            'communication_plan' => 'nullable|string',
        ]);
        $stakeholder->update($data);
        return redirect()->route('stakeholders.show', $stakeholder)->with('success','Stakeholder updated');
    }

    public function destroy(Stakeholder $stakeholder)
    {
        $stakeholder->delete();
        return redirect()->route('stakeholders.index')->with('success','Stakeholder deleted');
    }

    public function matrix(Request $request)
    {
        $projectId = $request->get('project_id');
        $stakeholders = Stakeholder::when($projectId, fn($q)=>$q->where('project_id',$projectId))->get();
        $projects = Project::all();
        return view('stakeholders.matrix', compact('stakeholders','projects','projectId'));
    }

    public function addComm(Request $request, Stakeholder $stakeholder)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'planned_at' => 'nullable|date',
        ]);
        $stakeholder->comms()->create($data);
        return back()->with('success','Communication logged');
    }
}
