<?php

namespace App\Http\Controllers;

use App\Models\ProductionBatch;
use App\Models\Project;
use App\Models\BpomRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductionBatchController extends Controller
{
    public function index()
    {
        $this->authorizeView();
        $batches = ProductionBatch::with(['project','bpom'])->orderByDesc('id')->paginate(15);
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
            'bpom_registration_id' => 'nullable|exists:bpom_registrations,id',
            'quantity_produced' => 'nullable|integer|min:0',
            'expiry_date' => 'nullable|date',
        ]);
        $batchNo = self::generateBatchNumber();
        $batch = ProductionBatch::create([
            'project_id' => $data['project_id'],
            'bpom_registration_id' => $data['bpom_registration_id'] ?? null,
            'batch_number' => $batchNo,
            'quantity_produced' => $data['quantity_produced'] ?? 0,
            'expiry_date' => $data['expiry_date'] ?? null,
            'qc_status' => 'pending',
        ]);
        return redirect()->route('batches.show', $batch)->with('success','Batch created');
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
            'bpom_registration_id' => 'nullable|exists:bpom_registrations,id',
            'quantity_produced' => 'nullable|integer|min:0',
            'expiry_date' => 'nullable|date',
            'qc_status' => 'required|in:pending,passed,failed',
        ]);
        $batch->update($data);
        return redirect()->route('batches.show', $batch)->with('success','Batch updated');
    }

    public function destroy(ProductionBatch $batch)
    {
        $this->authorizeDelete();
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


