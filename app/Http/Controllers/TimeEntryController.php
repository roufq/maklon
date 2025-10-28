<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TimeEntryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(TimeEntry::class, 'time_entry');
    }
    public function index()
    {
        $timeEntries = TimeEntry::with(['user', 'project', 'task'])
                               ->accessibleTo(Auth::user())
                               ->latest()
                               ->paginate(15);

        return view('time_entries.index', compact('timeEntries'));
    }

    public function create()
    {
        $projects = Project::all();
        $tasks = Task::all();

        return view('time_entries.create', compact('projects', 'tasks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'duration' => 'required|numeric|min:0',
            'billable' => 'boolean',
            'project_id' => 'nullable|exists:projects,id',
            'task_id' => 'nullable|exists:tasks,id',
        ]);

        $validated['user_id'] = Auth::id();

        TimeEntry::create($validated);

        return redirect()->route('time_entries.index')
                        ->with('success', 'Time entry created successfully.');
    }

    public function show(TimeEntry $timeEntry)
    {
        $timeEntry->load(['user', 'project', 'task']);
        return view('time_entries.show', compact('timeEntry'));
    }

    public function edit(TimeEntry $timeEntry)
    {
        $projects = Project::all();
        $tasks = Task::all();

        return view('time_entries.edit', compact('timeEntry', 'projects', 'tasks'));
    }

    public function update(Request $request, TimeEntry $timeEntry)
    {
        $validated = $request->validate([
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'duration' => 'required|numeric|min:0',
            'billable' => 'boolean',
            'project_id' => 'nullable|exists:projects,id',
            'task_id' => 'nullable|exists:tasks,id',
        ]);

        $timeEntry->update($validated);

        return redirect()->route('time_entries.index')
                        ->with('success', 'Time entry updated successfully.');
    }

    public function destroy(TimeEntry $timeEntry)
    {
        $timeEntry->delete();

        return redirect()->route('time_entries.index')
                        ->with('success', 'Time entry deleted successfully.');
    }

    public function startTimer(Request $request, Task $task)
    {
        // Prevent multiple running timers for this user
        $running = TimeEntry::where('user_id', Auth::id())
            ->whereNull('end_time')
            ->first();
        if ($running) {
            return back()->with('error', 'You already have a running timer. Stop it before starting a new one.');
        }

        TimeEntry::create([
            'description' => 'Timer for task #'.$task->id,
            'start_time' => now(),
            'end_time' => null,
            'duration' => 0,
            'billable' => true,
            'user_id' => Auth::id(),
            'project_id' => $task->project_id,
            'task_id' => $task->id,
        ]);

        return back()->with('success', 'Timer started.');
    }

    public function stopTimer(Request $request, Task $task)
    {
        $entry = TimeEntry::where('user_id', Auth::id())
            ->where('task_id', $task->id)
            ->whereNull('end_time')
            ->latest('start_time')
            ->first();

        if (!$entry) {
            return back()->with('error', 'No running timer found for this task.');
        }

        $end = now();
        $minutes = Carbon::parse($entry->start_time)->diffInMinutes($end);

        // Rounding
        $inc = (int) config('time.rounding_minutes', 15);
        $rounded = max(1, (int) (round($minutes / $inc) * $inc));

        $entry->end_time = $end;
        $entry->duration = $rounded; // store minutes

        // Billable amount using role-based rate if available
        $rateMap = config('time.rates_per_role', []);
        $role = optional(Auth::user()->roles->first())->name;
        $rate = $rateMap[$role] ?? 0;
        $entry->billable_rate = $rate;
        $entry->billable_amount = ($rate / 60) * $entry->duration;

        $entry->save();

        return back()->with('success', 'Timer stopped and time recorded (rounded to '.$inc.'m).');
    }
}
