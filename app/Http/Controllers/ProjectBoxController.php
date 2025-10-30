<?php

namespace App\Http\Controllers;

use App\Models\ProjectBox;
use App\Models\Project;
use App\Models\BoxType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ProjectBoxController extends Controller
{
    public function create(Request $request)
    {
        abort_unless(Gate::allows('permission','boxes.create'),403);
        $projectId = $request->get('project_id');
        $projects = Project::orderBy('name')->get(['id','name']);
        $boxTypes = BoxType::orderBy('name')->get(['id','name']);
        return view('project_boxes.create', compact('projects','boxTypes','projectId'));
    }

    public function store(Request $request)
    {
        abort_unless(Gate::allows('permission','boxes.create'),403);
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'box_type_id' => 'required|exists:box_types,id',
            'size' => 'nullable|string|max:255',
            'shape' => 'nullable|string|max:255',
            'mockup' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:20480',
        ]);
        $path = null;
        if ($request->hasFile('mockup')) {
            $path = $request->file('mockup')->store('project_boxes', 'public');
        }
        $box = ProjectBox::create([
            'project_id' => $data['project_id'],
            'box_type_id' => $data['box_type_id'],
            'size' => $data['size'] ?? null,
            'shape' => $data['shape'] ?? null,
            'mockup_path' => $path,
        ]);
        return redirect()->route('projects.show',$box->project_id)->with('success','Project Box created');
    }

    public function edit(ProjectBox $projectBox)
    {
        abort_unless(Gate::allows('permission','boxes.edit'),403);
        $projects = Project::orderBy('name')->get(['id','name']);
        $boxTypes = BoxType::orderBy('name')->get(['id','name']);
        return view('project_boxes.edit', compact('projectBox','projects','boxTypes'));
    }

    public function update(Request $request, ProjectBox $projectBox)
    {
        abort_unless(Gate::allows('permission','boxes.edit'),403);
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'box_type_id' => 'required|exists:box_types,id',
            'size' => 'nullable|string|max:255',
            'shape' => 'nullable|string|max:255',
            'mockup' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:20480',
        ]);
        if ($request->hasFile('mockup')) {
            $path = $request->file('mockup')->store('project_boxes', 'public');
            $projectBox->mockup_path = $path;
        }
        $projectBox->update([
            'project_id' => $data['project_id'],
            'box_type_id' => $data['box_type_id'],
            'size' => $data['size'] ?? null,
            'shape' => $data['shape'] ?? null,
        ]);
        $projectBox->save();
        return redirect()->route('projects.show',$projectBox->project_id)->with('success','Project Box updated');
    }

    public function destroy(ProjectBox $projectBox)
    {
        abort_unless(Gate::allows('permission','boxes.delete'),403);
        $projectId = $projectBox->project_id;
        $projectBox->delete();
        return redirect()->route('projects.show',$projectId)->with('success','Project Box deleted');
    }
}
