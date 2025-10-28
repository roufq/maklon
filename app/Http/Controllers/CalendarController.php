<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    /**
     * Display the calendar view.
     */
    public function index()
    {
        return view('calendar.index');
    }

    /**
     * Display a listing of calendar events.
     */
    public function events(Request $request)
    {
        $query = CalendarEvent::with(['project', 'user'])->accessibleTo(Auth::user());

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $events = $query->latest()->paginate(12);
        $projects = Project::accessibleTo(Auth::user())->get();

        return view('calendar.events.index', compact('events', 'projects'));
    }

    /**
     * Show the form for creating a new calendar event.
     */
    public function create()
    {
        $projects = Project::all();
        return view('calendar.events.create', compact('projects'));
    }

    /**
     * Store a newly created calendar event.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:meeting,milestone,reminder,task',
            'project_id' => 'nullable|exists:projects,id',
            'location' => 'nullable|string|max:255',
            'attendees' => 'nullable|string',
        ]);

        CalendarEvent::create([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'type' => $request->type,
            'user_id' => Auth::id(),
            'project_id' => $request->project_id,
        ]);

        return redirect()->route('calendar.events.index')
            ->with('success', 'Calendar event created successfully.');
    }

    /**
     * Display the specified calendar event.
     */
    public function show(CalendarEvent $event)
    {
        $event->load(['project', 'user']);
        return view('calendar.events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified calendar event.
     */
    public function edit(CalendarEvent $event)
    {
        $projects = Project::all();
        return view('calendar.events.edit', compact('event', 'projects'));
    }

    /**
     * Update the specified calendar event.
     */
    public function update(Request $request, CalendarEvent $event)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:meeting,milestone,reminder,task',
            'project_id' => 'nullable|exists:projects,id',
            'location' => 'nullable|string|max:255',
            'attendees' => 'nullable|string',
        ]);

        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'type' => $request->type,
            'project_id' => $request->project_id,
        ]);

        return redirect()->route('calendar.events.index')
            ->with('success', 'Calendar event updated successfully.');
    }

    /**
     * Remove the specified calendar event.
     */
    public function destroy(CalendarEvent $event)
    {
        $event->delete();

        return redirect()->route('calendar.events.index')
            ->with('success', 'Calendar event deleted successfully.');
    }

    /**
     * Get calendar events as JSON for FullCalendar.
     */
    public function getEvents(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $events = [];

        // Get calendar events
        $calendarEvents = CalendarEvent::with(['project'])->accessibleTo(Auth::user())
            ->whereBetween('start_date', [$start, $end])
            ->get();

        foreach ($calendarEvents as $event) {
            $events[] = [
                'id' => 'calendar_' . $event->id,
                'title' => $event->title,
                'start' => $event->start_date->toDateString(),
                'end' => $event->end_date->toDateString(),
                'backgroundColor' => $this->getEventColor($event->type),
                'borderColor' => $this->getEventColor($event->type),
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => $event->type,
                    'description' => $event->description,
                    'project' => $event->project ? $event->project->name : null,
                ],
            ];
        }

        // Get tasks with due dates
        $tasks = \App\Models\Task::with(['project', 'assignedUser'])->accessibleTo(Auth::user())
            ->whereBetween('due_date', [$start, $end])
            ->get();

        foreach ($tasks as $task) {
            $events[] = [
                'id' => 'task_' . $task->id,
                'title' => $task->title,
                'start' => $task->due_date->toDateString(),
                'end' => $task->due_date->toDateString(),
                'backgroundColor' => $this->getTaskColor($task),
                'borderColor' => $this->getTaskColor($task),
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'task',
                    'description' => $task->description,
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'project' => $task->project ? $task->project->name : null,
                    'assigned_to' => $task->assignedUser ? $task->assignedUser->name : null,
                ],
            ];
        }

        // Get projects with deadlines
        $projects = \App\Models\Project::accessibleTo(Auth::user())->whereNotNull('deadline')
            ->whereBetween('deadline', [$start, $end])
            ->get();

        foreach ($projects as $project) {
            $events[] = [
                'id' => 'project_' . $project->id,
                'title' => 'Project Deadline: ' . $project->name,
                'start' => $project->deadline->toDateString(),
                'end' => $project->deadline->toDateString(),
                'backgroundColor' => '#ffc107',
                'borderColor' => '#ffc107',
                'textColor' => '#000000',
                'extendedProps' => [
                    'type' => 'milestone',
                    'description' => 'Project deadline for ' . $project->name,
                    'project' => $project->name,
                ],
            ];
        }

        return response()->json($events);
    }

    /**
     * Store a new calendar event from calendar modal.
     */
    public function storeEvent(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:meeting,milestone,reminder,task',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        CalendarEvent::create([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'type' => $request->type,
            'user_id' => Auth::id(),
            'project_id' => $request->project_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully.',
        ]);
    }

    /**
     * Get color for calendar event based on type.
     */
    private function getEventColor($type)
    {
        switch($type) {
            case 'meeting': return '#007bff'; // Blue
            case 'milestone': return '#ffc107'; // Yellow
            case 'reminder': return '#6c757d'; // Gray
            case 'task': return '#28a745'; // Green
            default: return '#6c757d';
        }
    }

    /**
     * Get color for task based on status and priority.
     */
    private function getTaskColor($task)
    {
        if ($task->status === 'completed') {
            return '#28a745'; // Green
        } elseif ($task->status === 'in_progress') {
            return '#007bff'; // Blue
        } elseif ($task->priority === 'urgent') {
            return '#dc3545'; // Red
        } elseif ($task->priority === 'high') {
            return '#fd7e14'; // Orange
        } else {
            return '#6c757d'; // Gray
        }
    }
}
