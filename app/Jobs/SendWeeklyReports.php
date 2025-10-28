<?php

namespace App\Jobs;

use App\Mail\WeeklyReportMail;
use App\Models\User;
use App\Models\Task;
use App\Models\Risk;
use App\Models\TimeEntry;
use App\Models\ProjectBudget;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWeeklyReports implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $from = now()->subWeek()->startOfWeek();
        $to = now()->subWeek()->endOfWeek();

        // Build summary metrics reused from executive dashboard logic
        $completedCount = Task::where('status', 'completed')->count();
        $onTimeCount = Task::where('status', 'completed')
            ->whereNotNull('due_date')
            ->whereColumn('updated_at', '<=', 'due_date')
            ->count();
        $onTimeRate = $completedCount > 0 ? round(($onTimeCount / $completedCount) * 100, 1) : 0.0;

        $activeRisks = Risk::where('status', '!=', 'closed')->get();
        $highRisks = $activeRisks->filter(fn($r) => in_array($r->risk_level, ['High', 'Very High']))->count();

        $minutesByUser = TimeEntry::whereBetween('start_time', [$from, $to])
            ->selectRaw('user_id, SUM(duration_minutes) as minutes')
            ->groupBy('user_id')
            ->pluck('minutes');
        $workingDays = $from->diffInWeekdays($to) + 1;
        $denominator = max(1, $workingDays) * 8 * 60;
        $avgUtilization = $minutesByUser->count() > 0
            ? round($minutesByUser->map(fn($m) => ($m / $denominator) * 100)->avg(), 1)
            : 0.0;

        $topVariance = ProjectBudget::with('project')->get()
            ->map(function ($b) {
                return [
                    'project' => optional($b->project)->name ?? 'N/A',
                    'currency' => $b->currency,
                    'variance' => (float) ($b->total_budget - $b->spent_amount),
                ];
            })
            ->sortBy('variance')->take(5)->values()->all();

        $totalMinutes = (int) TimeEntry::whereBetween('start_time', [$from, $to])->sum('duration_minutes');
        $billableMinutes = (int) TimeEntry::whereBetween('start_time', [$from, $to])->where('is_billable', true)->sum('duration_minutes');

        $summary = [
            'period' => $from->toDateString() . ' to ' . $to->toDateString(),
            'total_hours' => number_format($totalMinutes / 60, 2),
            'billable_hours' => number_format($billableMinutes / 60, 2),
            'on_time_rate' => $onTimeRate,
            'high_risks' => $highRisks,
            'avg_utilization' => $avgUtilization,
            'top_variance' => $topVariance,
        ];

        $recipients = User::where('is_active', true)
            ->where(function ($q) {
                // prefer explicit opt-in/out; default is opt-in
                $q->whereNull('report_opt_out')->orWhere('report_opt_out', false);
            })
            ->get();

        foreach ($recipients as $user) {
            try {
                Mail::to($user->email)->send(new WeeklyReportMail($summary));
                Log::info('Weekly report email sent', ['user_id' => $user->id]);
            } catch (\Throwable $e) {
                Log::error('Weekly report email failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            }
        }
    }
}
