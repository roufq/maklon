<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectBudget;
use App\Models\BudgetCategory;
use App\Models\ProjectExpense;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BudgetController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:budgets.view')->only(['index','show']);
        $this->middleware('permission:budgets.create')->only(['create','store']);
        $this->middleware('permission:budgets.edit')->only(['storeCategory','storeExpense','approve']);
    }
    public function index()
    {
        $budgets = ProjectBudget::with(['project'])
            ->accessibleTo(auth()->user())
            ->latest()
            ->paginate(15);

        return view('budgets.index', compact('budgets'));
    }

    public function create()
    {
        $projects = Project::all();
        return view('budgets.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'total_budget' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'threshold_warning_percent' => 'nullable|numeric|min:0|max:100',
            'threshold_critical_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        ProjectBudget::create($validated);

        return redirect()->route('budgets.index')
            ->with('success', 'Project budget created successfully.');
    }

    public function show(ProjectBudget $budget)
    {
        $budget->load(['project', 'categories.expenses']);

        // Recalculate spent from expenses for accuracy
        $spent = ProjectExpense::where('project_id', $budget->project_id)->sum('amount');
        $budget->spent_amount = $spent;
        $budget->save();

        return view('budgets.show', compact('budget'));
    }

    public function storeCategory(Request $request, ProjectBudget $budget)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'allocated_amount' => 'required|numeric|min:0',
        ]);

        $validated['project_budget_id'] = $budget->id;
        BudgetCategory::create($validated);
        return redirect()->route('budgets.show', $budget)
            ->with('success', 'Category added.');
    }

    public function storeExpense(Request $request, ProjectBudget $budget)
    {
        $validated = $request->validate([
            'budget_category_id' => 'nullable|exists:budget_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'spent_at' => 'required|date',
            'billable' => 'nullable|boolean',
            'currency' => 'nullable|string|max:10',
            'description' => 'nullable|string',
        ]);

        $expense = ProjectExpense::create([
            'project_id' => $budget->project_id,
            'budget_category_id' => $validated['budget_category_id'] ?? null,
            'entered_by' => Auth::id(),
            'amount' => $validated['amount'],
            'spent_at' => $validated['spent_at'],
            'billable' => $validated['billable'] ?? false,
            'currency' => $validated['currency'] ?? $budget->currency,
            'description' => $validated['description'] ?? null,
        ]);

        // Update spent amounts (category + budget)
        if ($expense->budget_category_id) {
            $category = BudgetCategory::find($expense->budget_category_id);
            if ($category) {
                $category->spent_amount = ($category->spent_amount + $expense->amount);
                $category->save();

                // Category overspend alerts using budget thresholds
                $warn = (float) $budget->threshold_warning_percent;
                $crit = (float) $budget->threshold_critical_percent;
                if ($category->allocated_amount > 0) {
                    $pct = ($category->spent_amount / $category->allocated_amount) * 100;
                    foreach ([['key'=>'warning','th'=>$warn], ['key'=>'critical','th'=>$crit]] as $lv) {
                        if ($pct >= $lv['th']) {
                            $exists = Notification::where('type', 'budget_category_alert')
                                ->where('user_id', $budget->project->created_by)
                                ->where('notifiable_type', BudgetCategory::class)
                                ->where('notifiable_id', $category->id)
                                ->whereJsonContains('data->level', $lv['key'])
                                ->exists();
                            if (!$exists) {
                                Notification::create([
                                    'title' => 'Category '.ucfirst($lv['key']).' Alert',
                                    'message' => 'Category "'.$category->name.'" reached '.round($pct,1)."% of allocation.",
                                    'type' => 'budget_category_alert',
                                    'user_id' => $budget->project->created_by,
                                    'data' => [
                                        'level' => $lv['key'],
                                        'percent' => $pct,
                                        'threshold' => $lv['th'],
                                        'project_id' => $budget->project_id,
                                        'budget_id' => $budget->id,
                                    ],
                                    'notifiable_type' => BudgetCategory::class,
                                    'notifiable_id' => $category->id,
                                ]);
                            }
                        }
                    }
                }
            }
        }

        $budget->spent_amount = ($budget->spent_amount + $expense->amount);
        $budget->save();

        // Alerts when thresholds crossed
        $this->maybeAlertBudgetThreshold($budget);

        return redirect()->route('budgets.show', $budget)
            ->with('success', 'Expense recorded.');
    }

    protected function maybeAlertBudgetThreshold(ProjectBudget $budget): void
    {
        $percent = $budget->utilization_percent;
        $project = $budget->project;
        $userId = $project->created_by; // notify project owner

        $levels = [
            ['key' => 'warning', 'threshold' => (float) $budget->threshold_warning_percent],
            ['key' => 'critical', 'threshold' => (float) $budget->threshold_critical_percent],
        ];

        foreach ($levels as $level) {
            if ($percent >= $level['threshold']) {
                $exists = Notification::where('type', 'budget_alert')
                    ->where('user_id', $userId)
                    ->where('notifiable_type', ProjectBudget::class)
                    ->where('notifiable_id', $budget->id)
                    ->whereJsonContains('data->level', $level['key'])
                    ->exists();

                if (!$exists) {
                    Notification::create([
                        'title' => 'Budget '.ucfirst($level['key']).' Alert',
                        'message' => 'Project "'.$project->name.'" has reached '.round($percent,1)."% of its budget.",
                        'type' => 'budget_alert',
                        'user_id' => $userId,
                        'data' => [
                            'level' => $level['key'],
                            'percent' => $percent,
                            'threshold' => $level['threshold'],
                            'project_id' => $project->id,
                        ],
                        'notifiable_type' => ProjectBudget::class,
                        'notifiable_id' => $budget->id,
                    ]);
                }
            }
        }
    }

    public function approve(ProjectBudget $budget)
    {
        if ($budget->status === 'approved') {
            return back()->with('info', 'Budget already approved.');
        }

        $budget->status = 'approved';
        $budget->approved_by = Auth::id();
        $budget->approved_at = now();
        $budget->save();

        return back()->with('success', 'Budget approved.');
    }
}
