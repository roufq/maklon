<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\WorkStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
    }
    public function index()
    {
        $query = Task::with(['project', 'assignedUser', 'creator'])->accessibleTo(Auth::user());

        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('project_id')) {
            $query->where('project_id', request('project_id'));
        }

        if (request('search')) {
            $query->where('title', 'like', '%' . request('search') . '%');
        }

        $tasks = $query->latest()->paginate(15);

        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $projects = Project::all();
        $users = User::all();
        $parentTasks = Task::whereNull('parent_task_id')->get();
        $workStations = WorkStation::orderBy('name')->get(['id','name']);
        return view('tasks.create', compact('projects', 'users', 'parentTasks','workStations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,review,completed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'parent_task_id' => 'nullable|exists:tasks,id',
            'work_station_id' => 'nullable|exists:work_stations,id',
            'scheduled_start' => 'nullable|date',
            'scheduled_end' => 'nullable|date|after_or_equal:scheduled_start',
        ]);

        $validated['created_by'] = Auth::id();

        Task::create($validated);

        return redirect()->route('tasks.index')
                        ->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        $task->load(['project', 'assignedUser', 'creator', 'parentTask', 'subtasks', 'timeEntries.user']);
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $projects = Project::all();
        $users = User::all();
        $workStations = WorkStation::orderBy('name')->get(['id','name']);
        $parentTasks = Task::whereNull('parent_task_id')
                          ->where('id', '!=', $task->id)
                          ->get();

        return view('tasks.edit', compact('task', 'projects', 'users', 'parentTasks','workStations'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,review,completed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'parent_task_id' => 'nullable|exists:tasks,id',
            'work_station_id' => 'nullable|exists:work_stations,id',
            'scheduled_start' => 'nullable|date',
            'scheduled_end' => 'nullable|date|after_or_equal:scheduled_start',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.show', $task)
                        ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')
                        ->with('success', 'Task deleted successfully.');
    }
}
