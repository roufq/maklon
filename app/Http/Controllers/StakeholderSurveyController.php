<?php

namespace App\Http\Controllers;

use App\Models\StakeholderSurvey;
use App\Models\StakeholderSurveyResponse;
use App\Models\Project;
use App\Models\Stakeholder;
use Illuminate\Http\Request;

class StakeholderSurveyController extends Controller
{
    public function index()
    {
        $surveys = StakeholderSurvey::with('project','creator')->latest()->paginate(15);
        return view('stakeholders.surveys.index', compact('surveys'));
    }

    public function create()
    {
        $projects = Project::all();
        return view('stakeholders.surveys.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'questions' => 'required|array|min:1',
            'questions.*.label' => 'required|string|max:255',
            'questions.*.key' => 'required|string|max:50',
        ]);
        $survey = StakeholderSurvey::create([
            'project_id' => $data['project_id'],
            'created_by' => auth()->id(),
            'title' => $data['title'],
            'questions' => $data['questions'],
        ]);
        return redirect()->route('surveys.show', $survey)->with('success','Survey created');
    }

    public function show(StakeholderSurvey $survey)
    {
        $survey->load('project','responses.stakeholder');
        $stakeholders = Stakeholder::where('project_id', $survey->project_id)->get();
        return view('stakeholders.surveys.show', compact('survey','stakeholders'));
    }

    public function invite(Request $request, StakeholderSurvey $survey)
    {
        $request->validate(['stakeholder_id' => 'required|exists:stakeholders,id']);
        // create empty response placeholder; in real email scenario, send link
        StakeholderSurveyResponse::firstOrCreate([
            'survey_id' => $survey->id,
            'stakeholder_id' => $request->stakeholder_id,
        ]);
        return back()->with('success','Stakeholder invited (placeholder created)');
    }

    public function submit(Request $request, StakeholderSurveyResponse $response)
    {
        $data = $request->validate([
            'answers' => 'required|array',
            'rating' => 'nullable|numeric|min:1|max:5',
        ]);
        $response->answers = $data['answers'];
        $response->rating = $data['rating'] ?? null;
        $response->submitted_at = now();
        $response->save();
        return back()->with('success','Response submitted');
    }
}

