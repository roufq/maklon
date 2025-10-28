<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Risk;
use App\Models\TimeEntry;
use App\Models\ProjectBudget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function executive()
    {
        $cacheTtl = now()->addMinutes(5);

        $metrics = Cache::remember('exec_dashboard_metrics', $cacheTtl, function () {
            // On-time rate: completed tasks with updated_at <= due_date over completed tasks
            $completedCount = Task::where('status', 'completed')->count();
            $onTimeCount = Task::where('status', 'completed')
                ->whereNotNull('due_date')
                ->whereColumn('updated_at', '<=', 'due_date')
                ->count();
            $onTimeRate = $completedCount > 0 ? round(($onTimeCount / $completedCount) * 100, 1) : 0.0;

            // Active high risks: status != closed and risk level high or very high
            $activeRisks = Risk::where('status', '!=', 'closed')->get();
            $highRisks = $activeRisks->filter(function ($r) {
                return in_array($r->risk_level, ['High', 'Very High']);
            })->count();

            // Team utilization (avg for last 7 days vs 8h/day baseline)
            $from = now()->subDays(6)->startOfDay();
            $to = now()->endOfDay();
            $minutesByUser = TimeEntry::whereBetween('start_time', [$from, $to])
                ->selectRaw('user_id, SUM(duration_minutes) as minutes')
                ->groupBy('user_id')
                ->pluck('minutes');
            $workingDays = $from->diffInWeekdays($to) + 1; // approx; includes today
            $denominator = max(1, $workingDays) * 8 * 60; // minutes baseline
            $avgUtilization = $minutesByUser->count() > 0
                ? round($minutesByUser->map(fn($m) => ($m / $denominator) * 100)->avg(), 1)
                : 0.0;

            // Budget variance top 5 (largest negative variance)
            $topVariance = ProjectBudget::with('project')->get()
                ->map(function ($b) {
                    return [
                        'project' => optional($b->project)->name ?? 'N/A',
                        'currency' => $b->currency,
                        'total_budget' => (float) $b->total_budget,
                        'spent' => (float) $b->spent_amount,
                        'variance' => (float) ($b->total_budget - $b->spent_amount),
                        'utilization_percent' => round((float) $b->utilization_percent, 1),
                    ];
                })
                ->sortBy('variance') // most negative first (overspend)
                ->take(5)
                ->values();

            return [
                'on_time_rate' => $onTimeRate,
                'high_risks' => $highRisks,
                'avg_utilization' => $avgUtilization,
                'top_variance' => $topVariance,
            ];
        });

        // Role-based widget config
        $role = Auth::user()?->getRoleNames()?->first() ?? 'user';
        $config = config('dashboard.widgets');
        $allowed = $config[$role] ?? $config['default'];

        return view('dashboard.executive', [
            'metrics' => $metrics,
            'allowedWidgets' => $allowed,
        ]);
    }
}
