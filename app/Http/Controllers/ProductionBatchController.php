<?php

namespace App\Http\Controllers;

use App\Models\ProductionBatch;
use App\Models\Project;
use App\Models\BpomRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductionBatchController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeView();
        $batches = ProductionBatch::with(['project','bpom'])
            ->when($request->get('qc_status'), fn($q,$s)=>$q->where('qc_status',$s))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();
        return view('batches.index', compact('batches'));
    }

    public function create()
    {
        $this->authorizeCreate();
        $projects = Project::orderBy('name')->get(['id','name']);
        $bpoms = BpomRegistration::orderBy('product_name')->get(['id','product_name']);
        return view('batches.create', compact('projects','bpoms'));
    }

    public function store(Request $request)
    {
        $this->authorizeCreate();
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'bpom_registration_id' => 'required|exists:bpom_registrations,id',
            'batch_number' => 'required|string|max:255',
            'quantity_produced' => 'required|integer|min:0',
            'expiry_date' => 'nullable|date',
            'qc_status' => 'required|in:pending,passed,failed',
        ]);
        $batch = ProductionBatch::create($data);
        \App\Models\ProductionBatchAudit::create([
            'production_batch_id' => $batch->id,
            'user_id' => auth()->id(),
            'action' => 'created',
            'meta' => $batch->toArray(),
        ]);
        dispatch(new \App\Jobs\SendWebhookEvent('production.batch.created', [
            'id' => $batch->id,
            'project_id' => $batch->project_id,
            'batch_number' => $batch->batch_number,
        ]));
        return redirect()->route('batches.index')->with('success','Batch created');
    }

    public function show(ProductionBatch $batch)
    {
        $this->authorizeView();
        $batch->load(['project','bpom']);
        return view('batches.show', compact('batch'));
    }

    public function edit(ProductionBatch $batch)
    {
        $this->authorizeEdit();
        $projects = Project::orderBy('name')->get(['id','name']);
        $bpoms = BpomRegistration::orderBy('product_name')->get(['id','product_name']);
        return view('batches.edit', compact('batch','projects','bpoms'));
    }

    public function update(Request $request, ProductionBatch $batch)
    {
        $this->authorizeEdit();
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'bpom_registration_id' => 'required|exists:bpom_registrations,id',
            'batch_number' => 'required|string|max:255',
            'quantity_produced' => 'required|integer|min:0',
            'expiry_date' => 'nullable|date',
            'qc_status' => 'required|in:pending,passed,failed',
        ]);
        $before = $batch->getOriginal();
        $batch->update($data);
        \App\Models\ProductionBatchAudit::create([
            'production_batch_id' => $batch->id,
            'user_id' => auth()->id(),
            'action' => 'updated',
            'meta' => ['before' => $before, 'after' => $batch->getAttributes()],
        ]);
        dispatch(new \App\Jobs\SendWebhookEvent('production.batch.updated', [
            'id' => $batch->id,
            'project_id' => $batch->project_id,
            'qc_status' => $batch->qc_status,
        ]));
        return redirect()->route('batches.show', $batch)->with('success','Batch updated');
    }

    public function destroy(ProductionBatch $batch)
    {
        $this->authorizeDelete();
        \App\Models\ProductionBatchAudit::create([
            'production_batch_id' => $batch->id,
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'meta' => null,
        ]);
        dispatch(new \App\Jobs\SendWebhookEvent('production.batch.deleted', [
            'id' => $batch->id,
        ]));
        $batch->delete();
        return redirect()->route('batches.index')->with('success','Batch deleted');
    }

    private static function generateBatchNumber(): string
    {
        $prefix = date('ymd');
        $seq = str_pad((string)(ProductionBatch::whereDate('created_at', today())->count() + 1), 4, '0', STR_PAD_LEFT);
        return $prefix.'-'.$seq;
    }

    private function authorizeView(): void { abort_unless(auth()->user()?->can('production.view'), 403); }
    private function authorizeCreate(): void { abort_unless(auth()->user()?->can('production.create'), 403); }
    private function authorizeEdit(): void { abort_unless(auth()->user()?->can('production.edit'), 403); }
    private function authorizeDelete(): void { abort_unless(auth()->user()?->can('production.delete'), 403); }
}
