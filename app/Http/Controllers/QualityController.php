<?php

namespace App\Http\Controllers;

use App\Models\QualityCheckpoint;
use App\Models\QcResult;
use App\Models\Project;
use App\Models\ProductionBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class QualityController extends Controller
{
    // Checkpoints
    public function checkpointsIndex(){ $this->v(); $items = QualityCheckpoint::with('project')->orderByDesc('id')->paginate(15); return view('quality.checkpoints.index', compact('items')); }
    public function checkpointsCreate(){ $this->c(); $projects = Project::orderBy('name')->get(['id','name']); return view('quality.checkpoints.create', compact('projects')); }
    public function checkpointsStore(Request $r){ $this->c(); $d = $r->validate(['project_id'=>'nullable|exists:projects,id','name'=>'required','criteria'=>'array','required'=>'boolean']); $i = QualityCheckpoint::create(['project_id'=>$d['project_id']??null,'name'=>$d['name'],'criteria'=>$d['criteria']??[],'required'=>$d['required']??true]); return redirect()->route('quality.checkpoints.show',$i); }
    public function checkpointsShow(QualityCheckpoint $checkpoint){ $this->v(); return view('quality.checkpoints.show', compact('checkpoint')); }
    public function checkpointsEdit(QualityCheckpoint $checkpoint){ $this->e(); $projects = Project::orderBy('name')->get(['id','name']); return view('quality.checkpoints.edit', compact('checkpoint','projects')); }
    public function checkpointsUpdate(Request $r, QualityCheckpoint $checkpoint){ $this->e(); $d=$r->validate(['project_id'=>'nullable|exists:projects,id','name'=>'required','criteria'=>'array','required'=>'boolean']); $checkpoint->update(['project_id'=>$d['project_id']??null,'name'=>$d['name'],'criteria'=>$d['criteria']??[],'required'=>$d['required']??true]); return redirect()->route('quality.checkpoints.show',$checkpoint)->with('success','Updated'); }
    public function checkpointsDestroy(QualityCheckpoint $checkpoint){ $this->d(); $checkpoint->delete(); return redirect()->route('quality.checkpoints.index')->with('success','Deleted'); }

    // Results
    public function resultsIndex(){ $this->v(); $items = QcResult::with(['batch','checkpoint'])->orderByDesc('id')->paginate(15); return view('quality.results.index', compact('items')); }
    public function resultsCreate(){ $this->c(); $batches = ProductionBatch::orderByDesc('id')->get(['id','batch_number']); $checkpoints = QualityCheckpoint::orderBy('name')->get(['id','name']); return view('quality.results.create', compact('batches','checkpoints')); }
    public function resultsStore(Request $r){ $this->c(); $d = $r->validate(['production_batch_id'=>'required|exists:production_batches,id','quality_checkpoint_id'=>'required|exists:quality_checkpoints,id','status'=>'required|in:pass,fail','notes'=>'nullable|string']); $res = QcResult::create($d + ['by_user_id'=>auth()->id()]); return redirect()->route('quality.results.show',$res); }
    public function resultsShow(QcResult $result){ $this->v(); $result->load(['batch','checkpoint']); return view('quality.results.show', compact('result')); }
    public function resultsEdit(QcResult $result){ $this->e(); $batches = ProductionBatch::orderByDesc('id')->get(['id','batch_number']); $checkpoints = QualityCheckpoint::orderBy('name')->get(['id','name']); return view('quality.results.edit', compact('result','batches','checkpoints')); }
    public function resultsUpdate(Request $r, QcResult $result){ $this->e(); $d = $r->validate(['production_batch_id'=>'required|exists:production_batches,id','quality_checkpoint_id'=>'required|exists:quality_checkpoints,id','status'=>'required|in:pass,fail','notes'=>'nullable|string']); $result->update($d); return redirect()->route('quality.results.show',$result)->with('success','Updated'); }
    public function resultsDestroy(QcResult $result){ $this->d(); $result->delete(); return redirect()->route('quality.results.index')->with('success','Deleted'); }

    private function v(){ abort_unless(Gate::allows('permission','qc.view'),403);} private function c(){ abort_unless(Gate::allows('permission','qc.create'),403);} private function e(){ abort_unless(Gate::allows('permission','qc.edit'),403);} private function d(){ abort_unless(Gate::allows('permission','qc.delete'),403);} 
}


