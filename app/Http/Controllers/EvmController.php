<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\ProjectBaseline;
use App\Models\EvmPoint;
use App\Models\ProjectBudget;
use App\Models\ProjectExpense;
use Illuminate\Http\Request;

class EvmController extends Controller
{
    public function index(Request $request)
    {
        $projectId = $request->get('project_id');
        $projects = Project::all();
        $project = $projectId ? Project::find($projectId) : $projects->first();
        $baselines = $project ? ProjectBaseline::where('project_id',$project->id)->orderBy('baseline_date','desc')->get() : collect();
        $points = $project ? EvmPoint::where('project_id',$project->id)->orderBy('as_of_date')->get() : collect();
        return view('evm.index', compact('projects','project','baselines','points'));
    }

    public function storeBaseline(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'baseline_date' => 'required|date',
            'description' => 'nullable|string',
        ]);
        ProjectBaseline::create($data);
        return back()->with('success','Baseline created');
    }

    public function capturePoint(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'baseline_id' => 'nullable|exists:project_baselines,id',
            'as_of_date' => 'required|date',
        ]);

        $projectId = (int) $data['project_id'];
        $asOf = $data['as_of_date'];
        $baselineId = $data['baseline_id'] ?? null;

        $totalTasks = Task::where('project_id', $projectId)->count();
        $completedTasks = Task::where('project_id', $projectId)->where('status','completed')->count();
        $dueByDate = Task::where('project_id', $projectId)->whereNotNull('due_date')->whereDate('due_date','<=',$asOf)->count();

        $budget = ProjectBudget::where('project_id',$projectId)->first();
        $bac = $budget ? (float)$budget->total_budget : 0.0; // Budget At Completion

        // Approximate PV and EV
        $pvPct = $totalTasks > 0 ? min(1, $dueByDate / $totalTasks) : 0;
        $evPct = $totalTasks > 0 ? min(1, $completedTasks / $totalTasks) : 0;

        $pv = round($bac * $pvPct, 2);
        $ev = round($bac * $evPct, 2);

        $ac = round((float) ProjectExpense::where('project_id',$projectId)
            ->whereDate('spent_at','<=',$asOf)
            ->sum('amount'), 2);

        EvmPoint::updateOrCreate(
            ['project_id' => $projectId, 'as_of_date' => $asOf],
            ['baseline_id' => $baselineId, 'pv' => $pv, 'ev' => $ev, 'ac' => $ac]
        );

        return back()->with('success','EVM point captured');
    }
}

