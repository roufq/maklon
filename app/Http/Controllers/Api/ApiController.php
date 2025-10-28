<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\ProjectBudget;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function projects(Request $request)
    {
        return Project::accessibleTo(auth()->user())->paginate(50);
    }

    public function projectShow(Project $project)
    {
        $this->authorize('view', $project);
        return $project->load('creator','team');
    }

    public function tasks(Request $request)
    {
        return Task::accessibleTo(auth()->user())->paginate(50);
    }

    public function taskShow(Task $task)
    {
        $this->authorize('view', $task);
        return $task->load('project','assignedUser');
    }

    public function timeEntries(Request $request)
    {
        $q = TimeEntry::accessibleTo(auth()->user());
        if ($request->project_id) $q->where('project_id', $request->project_id);
        if ($request->user_id) $q->where('user_id', $request->user_id);
        return $q->paginate(50);
    }

    public function budgets(Request $request)
    {
        return ProjectBudget::with('project')->accessibleTo(auth()->user())->paginate(50);
    }

    // Calendar
    public function calendarList(Request $request)
    {
        $q = CalendarEvent::accessibleTo(auth()->user());
        if ($request->project_id) $q->where('project_id', $request->project_id);
        return $q->orderBy('start_date','desc')->paginate(50);
    }

    public function calendarCreate(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:meeting,milestone,reminder,task',
            'project_id' => 'nullable|exists:projects,id',
        ]);
        $data['user_id'] = auth()->id() ?? 1;
        $event = CalendarEvent::create($data);
        return response()->json($event, 201);
    }
}
