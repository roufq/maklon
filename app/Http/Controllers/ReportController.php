<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\Team;
use App\Models\User;
use App\Models\Delivery;
use App\Models\BpomRegistration;
use App\Models\ProductionBatch;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function bpomCompliance(Request $request)
    {
        $status = $request->get('status');
        $withinDays = (int)($request->get('within_days', 0));

        $query = BpomRegistration::query();
        if ($status) { $query->where('status', $status); }
        if ($withinDays > 0) {
            $query->whereNotNull('expiry_date')
                  ->whereDate('expiry_date', '<=', now()->addDays($withinDays)->toDateString());
        }
        $items = $query->orderBy('expiry_date')->get();

        return view('reports.bpom-compliance', [
            'items' => $items,
            'status' => $status,
            'withinDays' => $withinDays,
        ]);
    }

    public function batchQcStatus(Request $request)
    {
        $projectId = $request->get('project_id');
        $status = $request->get('qc_status');

        $query = ProductionBatch::with(['project','bpom'])
            ->withCount([
                'qcResults as pass_count' => fn($q) => $q->where('status','pass'),
                'qcResults as fail_count' => fn($q) => $q->where('status','fail'),
                'qcResults as total_checks'
            ]);

        if ($projectId) { $query->where('project_id', $projectId); }
        if ($status) { $query->where('qc_status', $status); }

        $batches = $query->orderByDesc('id')->paginate(20)->withQueryString();
        $projects = \App\Models\Project::orderBy('name')->get(['id','name']);

        return view('reports.batch-qc', compact('batches','projects','projectId','status'));
    }

    public function inventoryHealth(Request $request)
    {
        $days = (int) ($request->get('days', 30));
        $from = now()->subDays(max(1,$days))->startOfDay();
        $to = now();

        $items = InventoryItem::with('supplier')->orderBy('name')->get();

        // Sum outs per item for turnover calc
        $outs = StockMovement::select('inventory_item_id', DB::raw('SUM(qty) as qty'))
            ->where('type','out')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('inventory_item_id')
            ->pluck('qty','inventory_item_id');

        $summary = $items->map(function($it) use ($outs, $days) {
            $outQty = (int) ($outs[$it->id] ?? 0);
            $avgDaily = $days > 0 ? ($outQty / $days) : 0;
            $daysOfStock = $avgDaily > 0 ? round($it->current_stock / $avgDaily, 1) : null;
            $low = ($it->min_stock !== null) ? ($it->current_stock < $it->min_stock) : false;
            return [
                'item' => $it,
                'outQty' => $outQty,
                'avgDaily' => round($avgDaily,2),
                'daysOfStock' => $daysOfStock,
                'isLow' => $low,
            ];
        });

        return view('reports.inventory-health', [
            'summary' => $summary,
            'days' => $days,
        ]);
    }

    public function projectProgress(Request $request)
    {
        $projects = Project::with(['tasks', 'team', 'creator'])
            ->when($request->status, function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->team_id, function ($query) use ($request) {
                return $query->where('team_id', $request->team_id);
            })
            ->when($request->date_from, function ($query) use ($request) {
                return $query->where('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($query) use ($request) {
                return $query->where('created_at', '<=', $request->date_to);
            })
            ->get();

        $teams = Team::all();

        return view('reports.project-progress', compact('projects', 'teams'));
    }

    public function timeTracking(Request $request)
    {
        $query = TimeEntry::with(['user', 'project', 'task'])
            ->when($request->user_id, function ($query) use ($request) {
                return $query->where('user_id', $request->user_id);
            })
            ->when($request->project_id, function ($query) use ($request) {
                return $query->where('project_id', $request->project_id);
            })
            ->when($request->date_from, function ($query) use ($request) {
                return $query->where('start_time', '>=', $request->date_from . ' 00:00:00');
            })
            ->when($request->date_to, function ($query) use ($request) {
                return $query->where('start_time', '<=', $request->date_to . ' 23:59:59');
            });

        $timeEntries = $query->get();

        // Calculate summary statistics
        $totalHours = $timeEntries->sum('duration') / 60;
        $billableHours = $timeEntries->where('billable', true)->sum('duration') / 60;
        $nonBillableHours = $totalHours - $billableHours;

        // Group by user
        $userSummary = $timeEntries->groupBy('user.name')->map(function ($entries) {
            return [
                'total_hours' => $entries->sum('duration') / 60,
                'billable_hours' => $entries->where('billable', true)->sum('duration') / 60,
                'entries_count' => $entries->count(),
            ];
        });

        // Group by project
        $projectSummary = $timeEntries->groupBy('project.name')->map(function ($entries) {
            return [
                'total_hours' => $entries->sum('duration') / 60,
                'billable_hours' => $entries->where('billable', true)->sum('duration') / 60,
                'entries_count' => $entries->count(),

    public function deliveryStatus(Request $request)
    {
        $from = $request->date_from ?: now()->subDays(30)->toDateString();
        $to = $request->date_to ?: now()->toDateString();
        $deliveries = \App\Models\Delivery::whereBetween('created_at', ["$from 00:00:00","$to 23:59:59"]) ->get();
        $byStatus = $deliveries->groupBy('status')->map->count();
        $byDay = $deliveries->groupBy(fn($d) => $d->created_at->format('Y-m-d'))->map->count();
        return view('reports.delivery-status', compact('byStatus','byDay','from','to'));
    }
            ];
        });

        $users = User::all();
        $projects = Project::all();

        return view('reports.time-tracking', compact(
            'timeEntries',
            'totalHours',
            'billableHours',
            'nonBillableHours',
            'userSummary',
            'projectSummary',
            'users',
            'projects'
        ));
    }

    public function teamPerformance(Request $request)
    {
        $teams = Team::with(['projects.tasks', 'members'])->get();

        $teamStats = $teams->map(function ($team) {
            $projects = $team->projects;
            $totalTasks = $projects->sum(function ($project) {
                return $project->tasks->count();
            });
            $completedTasks = $projects->sum(function ($project) {
                return $project->tasks->where('status', 'completed')->count();
            });

            $totalTime = $projects->sum('total_time');
            $activeProjects = $projects->where('status', 'active')->count();

            return [
                'team' => $team,
                'total_projects' => $projects->count(),
                'active_projects' => $activeProjects,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0,
                'total_time' => $totalTime,
                'member_count' => $team->members->count(),
            ];
        });

        return view('reports.team-performance', compact('teamStats'));
    }

    public function exportProjectProgress(Request $request)
    {
        $baseQuery = Project::with(['tasks', 'team', 'creator'])
            ->when($request->status, function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->team_id, function ($query) use ($request) {
                return $query->where('team_id', $request->team_id);
            })
            ->orderBy('id');

        $filename = 'project-progress-report-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($baseQuery) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Project Name',
                'Status',
                'Team',
                'Creator',
                'Total Tasks',
                'Completed Tasks',
                'Progress (%)',
                'Total Time (hours)',
                'Created At'
            ]);

            $baseQuery->clone()->chunkById(1000, function ($projects) use ($file) {
                foreach ($projects as $project) {
                    fputcsv($file, [
                        $project->name,
                        ucfirst($project->status),
                        $project->team->name ?? 'N/A',
                        $project->creator->name,
                        $project->tasks->count(),
                        $project->tasks->where('status', 'completed')->count(),
                        $project->progress,
                        $project->total_time / 60,
                        $project->created_at->format('Y-m-d')
                    ]);
                }
                if (function_exists('ob_flush')) { @ob_flush(); }
                flush();
            }, 'id');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportTimeTracking(Request $request)
    {
        $baseQuery = TimeEntry::with(['user', 'project', 'task'])
            ->when($request->user_id, function ($query) use ($request) {
                return $query->where('user_id', $request->user_id);
            })
            ->when($request->project_id, function ($query) use ($request) {
                return $query->where('project_id', $request->project_id);
            })
            ->when($request->date_from, function ($query) use ($request) {
                return $query->where('start_time', '>=', $request->date_from . ' 00:00:00');
            })
            ->when($request->date_to, function ($query) use ($request) {
                return $query->where('start_time', '<=', $request->date_to . ' 23:59:59');
            })
            ->orderBy('id');

        $filename = 'time-tracking-report-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($baseQuery) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'User',
                'Project',
                'Task',
                'Start Time',
                'End Time',
                'Duration (hours)',
                'Billable',
                'Description'
            ]);

            $baseQuery->clone()->chunkById(2000, function ($entries) use ($file) {
                foreach ($entries as $entry) {
                    fputcsv($file, [
                        $entry->user->name,
                        $entry->project->name ?? 'N/A',
                        $entry->task->title ?? 'N/A',
                        optional($entry->start_time)->format('Y-m-d H:i:s'),
                        optional($entry->end_time)->format('Y-m-d H:i:s') ?? 'Running',
                        number_format($entry->duration / 60, 2),
                        $entry->billable ? 'Yes' : 'No',
                        $entry->description
                    ]);
                }
                if (function_exists('ob_flush')) { @ob_flush(); }
                flush();
            }, 'id');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function financial(Request $request)
    {
        $projects = Project::with(['creator'])
            ->get();

        // Join budgets and compute variance
        $budgets = \App\Models\ProjectBudget::with('project')
            ->get()
            ->map(function ($b) {
                return [
                    'project' => $b->project->name,
                    'currency' => $b->currency,
                    'total_budget' => (float) $b->total_budget,
                    'spent' => (float) $b->spent_amount,
                    'variance' => (float) ($b->total_budget - $b->spent_amount),
                    'utilization_percent' => round($b->utilization_percent, 1),
                ];
            });

        // Simple monthly forecast: last 30 days average daily spend * remaining days this month
        $from = now()->subDays(30)->startOfDay();
        $to = now();
        $expenses = \App\Models\ProjectExpense::whereBetween('spent_at', [$from, $to])->get();
        $days = max(1, $from->diffInDays($to));
        $avgDaily = $expenses->sum('amount') / $days;
        $remainingDays = now()->endOfMonth()->diffInDays(now()) + 1;
        $forecastTotal = $avgDaily * $remainingDays;

        return view('reports.financial', [
            'budgets' => $budgets,
            'avgDailySpend' => round($avgDaily, 2),
            'forecastRemainder' => round($forecastTotal, 2),
        ]);
    }

    public function clientSummary(\App\Models\Project $project, Request $request)
    {
        if (!$request->hasValidSignature()) {
            abort(403);
        }

        $totalTasks = $project->tasks()->count();
        $completedTasks = $project->tasks()->where('status','completed')->count();
        $recentHours = TimeEntry::where('project_id',$project->id)
            ->whereBetween('start_time', [now()->subDays(30), now()])
            ->sum('duration_minutes') / 60;

        $budget = \App\Models\ProjectBudget::where('project_id',$project->id)->first();
        $budgetData = $budget ? [
            'currency' => $budget->currency,
            'total' => (float)$budget->total_budget,
            'spent' => (float)$budget->spent_amount,
            'variance' => (float)($budget->total_budget - $budget->spent_amount),
        ] : null;

        return view('public.project-summary', [
            'project' => $project,
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
            'recentHours' => round($recentHours,2),
            'budget' => $budgetData,
        ]);
    }

    public function stakeholderEngagement(Request $request)
    {
        $projectId = $request->get('project_id');
        $surveys = \App\Models\StakeholderSurvey::with('project','responses')
            ->when($projectId, fn($q)=>$q->where('project_id',$projectId))
            ->latest()->get();

        $summary = $surveys->map(function($s){
            $total = max(1, $s->responses->count());
            $submitted = $s->responses->whereNotNull('submitted_at')->count();
            $avg = (float) $s->responses->whereNotNull('rating')->avg('rating');
            return [
                'survey' => $s,
                'engagement_rate' => round(($submitted / $total) * 100, 1),
                'avg_rating' => round($avg, 2),
                'responses' => $submitted,
                'total' => $total,
            ];
        });

        $projects = \App\Models\Project::all();
        return view('reports.stakeholder-engagement', compact('summary','projects','projectId'));
    }

    public function deliveryStatus(\Illuminate\Http\Request )
    {
         = ->date_from ?: now()->subDays(30)->toDateString();
         = ->date_to ?: now()->toDateString();
         = \App\Models\Delivery::whereBetween('created_at', [" 00:00:00"," 23:59:59"]) ->get();
         = ->groupBy('status')->map->count();
         = ->groupBy(function(){ return ->created_at->format('Y-m-d'); })->map->count();
        return view('reports.delivery-status', compact('byStatus','byDay','from','to'));
    }}





