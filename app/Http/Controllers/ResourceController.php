<?php

namespace App\Http\Controllers;

use App\Models\ResourceAllocation;
use App\Models\Project;
use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\Team;

class ResourceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ResourceAllocation::class, 'resource');
    }
    /**
     * Display a listing of resource allocations.
     */
    public function index(Request $request): View
    {
        $query = ResourceAllocation::with(['project', 'user', 'task'])->accessibleTo(auth()->user());

        // Filter by project
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
            $query->byDateRange($request->start_date, $request->end_date);
        }

        // Filter by allocation type
        if ($request->has('allocation_type') && $request->allocation_type) {
            $query->where('allocation_type', $request->allocation_type);
        }

        $allocations = $query->orderBy('start_date', 'desc')->paginate(15);
        $projects = Project::all();
        $users = User::all();

        return view('resources.index', compact('allocations', 'projects', 'users'));
    }

    /**
     * Show resource reports page.
     */
    public function reports(): View
    {
        return view('resources.reports');
    }

    /**
     * Show the form for creating a new resource allocation.
     */
    public function create(Request $request): View
    {
        $projects = Project::all();
        $users = User::all();
        $tasks = collect();

        if ($request->has('project_id')) {
            $tasks = Task::where('project_id', $request->project_id)->get();
        }

        $selectedProject = $request->has('project_id') ? Project::find($request->project_id) : null;

        return view('resources.create', compact('projects', 'users', 'tasks', 'selectedProject'));
    }

    /**
     * Store a newly created resource allocation in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|exists:users,id',
            'task_id' => 'nullable|exists:tasks,id',
            'allocated_hours' => 'required|numeric|min:0.5|max:24',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'allocation_type' => 'required|in:planned,actual,forecast',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check for conflicts
        $conflict = ResourceAllocation::where('user_id', $validated['user_id'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                      ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                      ->orWhere(function ($q) use ($validated) {
                          $q->where('start_date', '<=', $validated['start_date'])
                            ->where('end_date', '>=', $validated['end_date']);
                      });
            })
            ->exists();

        if ($conflict) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Resource allocation conflicts with existing allocation for this user in the selected date range.');
        }

        ResourceAllocation::create($validated);

        return redirect()->route('resources.index')
            ->with('success', 'Resource allocation has been created successfully.');
    }

    /**
     * Display the specified resource allocation.
     */
    public function show(ResourceAllocation $resource): View
    {
        $resource->load(['project', 'user', 'task']);

        return view('resources.show', compact('resource'));
    }

    /**
     * Show the form for editing the specified resource allocation.
     */
    public function edit(ResourceAllocation $resource): View
    {
        $projects = Project::all();
        $users = User::all();
        $tasks = Task::where('project_id', $resource->project_id)->get();

        return view('resources.edit', compact('resource', 'projects', 'users', 'tasks'));
    }

    /**
     * Update the specified resource allocation in storage.
     */
    public function update(Request $request, ResourceAllocation $resource): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|exists:users,id',
            'task_id' => 'nullable|exists:tasks,id',
            'allocated_hours' => 'required|numeric|min:0.5|max:24',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'allocation_type' => 'required|in:planned,actual,forecast',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check for conflicts (excluding current allocation)
        $conflict = ResourceAllocation::where('user_id', $validated['user_id'])
            ->where('id', '!=', $resource->id)
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                      ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                      ->orWhere(function ($q) use ($validated) {
                          $q->where('start_date', '<=', $validated['start_date'])
                            ->where('end_date', '>=', $validated['end_date']);
                      });
            })
            ->exists();

        if ($conflict) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Resource allocation conflicts with existing allocation for this user in the selected date range.');
        }

        $resource->update($validated);

        return redirect()->route('resources.index')
            ->with('success', 'Resource allocation has been updated successfully.');
    }

    /**
     * Remove the specified resource allocation from storage.
     */
    public function destroy(ResourceAllocation $resource): RedirectResponse
    {
        $resource->delete();

        return redirect()->route('resources.index')
            ->with('success', 'Resource allocation has been deleted successfully.');
    }

    /**
     * Get resource utilization report.
     */
    public function utilization(Request $request): View
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $excludeWeekends = filter_var($request->get('exclude_weekends', false), FILTER_VALIDATE_BOOLEAN);

        $utilization = ResourceAllocation::with(['user', 'project'])
            ->byDateRange($startDate, $endDate)
            ->select('user_id', DB::raw('SUM(allocated_hours) as total_allocated_hours'))
            ->groupBy('user_id')
            ->get()
            ->map(function ($allocation) use ($startDate, $endDate, $excludeWeekends) {
                $workingDays = $this->workingDaysBetween($startDate, $endDate, $excludeWeekends);
                $workingHours = $workingDays * 8; // Assuming 8 hours per day
                $allocation->utilization_percentage = ($allocation->total_allocated_hours / $workingHours) * 100;
                return $allocation;
            });

        return view('resources.utilization', compact('utilization', 'startDate', 'endDate', 'excludeWeekends'));
    }

    /**
     * Get tasks for a specific project (AJAX).
     */
    public function getTasks(Request $request)
    {
        $projectId = $request->get('project_id');
        $tasks = Task::where('project_id', $projectId)->get(['id', 'title']);

        return response()->json($tasks);
    }

    /**
     * Team capacity dashboard with simple balancing suggestions.
     */
    public function capacity(Request $request): View
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $teamId = $request->get('team_id');
        $excludeWeekends = filter_var($request->get('exclude_weekends', false), FILTER_VALIDATE_BOOLEAN);

        $teams = Team::with('members')->get();
        $selectedTeam = $teamId ? $teams->firstWhere('id', (int) $teamId) : null;
        $members = $selectedTeam ? $selectedTeam->members : collect();

        // Utilization per member
        $utilization = collect();
        if ($selectedTeam) {
            $utilization = ResourceAllocation::with(['user'])
                ->byDateRange($startDate, $endDate)
                ->whereIn('user_id', $members->pluck('id'))
                ->select('user_id', DB::raw('SUM(allocated_hours) as total_allocated_hours'))
                ->groupBy('user_id')
                ->get()
                ->mapWithKeys(function ($row) use ($startDate, $endDate, $excludeWeekends) {
                    $workingDays = $this->workingDaysBetween($startDate, $endDate, $excludeWeekends);
                    $workingHours = $workingDays * 8;
                    $pct = $workingHours > 0 ? ($row->total_allocated_hours / $workingHours) * 100 : 0;
                    return [$row->user_id => round($pct, 1)];
                });
        }

        // Simple balancing suggestions: move hours from >100% users to <80% within same project
        $suggestions = [];
        if ($selectedTeam) {
            $overloaded = $utilization->filter(fn($pct) => $pct > 100)->keys();
            $underutil = $utilization->filter(fn($pct) => $pct < 80)->keys();

            foreach ($overloaded as $userId) {
                // Find this user's allocations in period
                $allocs = ResourceAllocation::byDateRange($startDate, $endDate)
                    ->where('user_id', $userId)
                    ->orderBy('start_date')
                    ->get();

                foreach ($allocs as $alloc) {
                    // Propose reassign to an underutilized teammate for same project
                    $targetUserId = $underutil->first(function ($uid) use ($selectedTeam, $userId) {
                        return $uid !== $userId && $selectedTeam->members->pluck('id')->contains($uid);
                    });

                    if ($targetUserId) {
                        $suggestions[] = [
                            'project' => $alloc->project->name ?? 'N/A',
                            'from_user_id' => $userId,
                            'to_user_id' => $targetUserId,
                            'task' => $alloc->task->title ?? null,
                            'proposed_hours' => min(4, (float) $alloc->allocated_hours),
                            'date_range' => $alloc->start_date->format('Y-m-d') . ' to ' . $alloc->end_date->format('Y-m-d'),
                        ];
                    }
                }
            }
        }

        return view('resources.capacity', [
            'teams' => $teams,
            'selectedTeam' => $selectedTeam,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'excludeWeekends' => $excludeWeekends,
            'utilization' => $utilization,
            'suggestions' => $suggestions,
        ]);
    }

    private function workingDaysBetween(string $startDate, string $endDate, bool $excludeWeekends): int
    {
        if (!$excludeWeekends) {
            return \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;
        }
        $start = \Carbon\Carbon::parse($startDate)->startOfDay();
        $end = \Carbon\Carbon::parse($endDate)->startOfDay();
        $days = 0;
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if (!$date->isWeekend()) {
                $days++;
            }
        }
        return max(0, $days);
    }
}
