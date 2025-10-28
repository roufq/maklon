<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class KanbanController extends Controller
{
    public function index(Request $request)
    {
        $projectId = $request->get('project_id');
        if ($projectId !== null) {
            Session::put('kanban.project_id', $projectId);
        } else {
            $projectId = Session::get('kanban.project_id');
        }

        $query = Task::with(['project', 'assignedUser']);
        if ($projectId) {
            $query->where('project_id', $projectId);
        }
        $tasks = $query->get()->groupBy('status');

        $projects = Project::all();
        $wipLimits = config('kanban.wip_limits');

        return view('kanban.index', [
            'tasks' => $tasks,
            'projects' => $projects,
            'projectId' => $projectId,
            'wipLimits' => $wipLimits,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    public function updateStatus(Request $request, Task $task)
    {
        $data = $request->validate([
            'status' => 'required|string|in:pending,in_progress,review,completed,cancelled'
        ]);

        $newStatus = $data['status'];
        $limits = config('kanban.wip_limits');
        $limit = $limits[$newStatus] ?? 999;

        // Enforce WIP limit per column
        $currentCount = Task::where('status', $newStatus)->count();
        if ($currentCount >= $limit) {
            return response()->json([
                'success' => false,
                'message' => 'WIP limit reached for "'.$newStatus.'"'
            ], 422);
        }

        $task->status = $newStatus;
        $task->save();

        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
