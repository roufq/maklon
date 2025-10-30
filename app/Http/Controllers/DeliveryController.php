<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Project;
use App\Models\ProductionBatch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $this->v();
        $deliveries = Delivery::with(['project','batch','customer'])
            ->when($request->get('status'), fn($q,$s)=>$q->where('status',$s))
            ->latest()
            ->paginate(15)
            ->withQueryString();
        return view('deliveries.index', compact('deliveries'));
    }

    public function create()
    {
        $this->c();
        $projects = Project::orderBy('name')->get(['id','name']);
        $batches = ProductionBatch::where('qc_status','passed')->orderByDesc('id')->get(['id','batch_number']);
        $customers = User::role('CS')->orderBy('name')->get(['id','name']);
        return view('deliveries.create', compact('projects','batches','customers'));
    }

    public function store(Request $request)
    {
        $this->c();
        // QC gating first so test sees gating error even if other fields are missing
        if ($request->filled('production_batch_id')) {
            $batch = ProductionBatch::find($request->input('production_batch_id'));
            if ($batch && $batch->qc_status !== 'passed') {
                return back()->with('error','Batch not passed QC')->withInput();
            }
        }
        $data = $request->validate([
            'production_batch_id' => 'required|exists:production_batches,id',
            'customer_id' => 'required|exists:users,id',
            'quantity' => 'required|integer|min:1',
            'shipping_address' => 'nullable|string',
            'tracking_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $delivery = Delivery::create([
            'project_id' => ProductionBatch::find($data['production_batch_id'])->project_id,
            'production_batch_id' => $data['production_batch_id'],
            'customer_id' => $data['customer_id'],
            'quantity' => $data['quantity'],
            'status' => 'pending',
            'tracking_number' => $data['tracking_number'] ?? null,
            'shipping_address' => $data['shipping_address'] ?? null,
            'notes' => $data['notes'] ?? null,
            'public_token' => Str::random(40),
        ]);
        return redirect()->route('deliveries.show',$delivery)->with('success','Delivery created');
    }

    public function show(Delivery $delivery)
    {
        $this->v();
        $delivery->load(['project','batch','customer']);
        return view('deliveries.show', compact('delivery'));
    }

    public function edit(Delivery $delivery)
    {
        $this->e();
        $projects = Project::orderBy('name')->get(['id','name']);
        $batches = ProductionBatch::orderByDesc('id')->get(['id','batch_number','qc_status']);
        $customers = User::role('CS')->orderBy('name')->get(['id','name']);
        return view('deliveries.edit', compact('delivery','projects','batches','customers'));
    }

    public function update(Request $request, Delivery $delivery)
    {
        $this->e();
        $data = $request->validate([
            'production_batch_id' => 'required|exists:production_batches,id',
            'customer_id' => 'required|exists:users,id',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|in:pending,ready,shipped,delivered,returned,rejected',
            'tracking_number' => 'nullable|string',
            'shipping_address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        if (!empty($data['production_batch_id'])) {
            $batch = ProductionBatch::findOrFail($data['production_batch_id']);
            if ($batch->qc_status !== 'passed') {
                return back()->with('error','Batch not passed QC')->withInput();
            }
        }
        $delivery->update([
            'project_id' => ProductionBatch::find($data['production_batch_id'])->project_id,
            'production_batch_id' => $data['production_batch_id'],
            'customer_id' => $data['customer_id'],
            'quantity' => $data['quantity'],
            'status' => $data['status'],
            'tracking_number' => $data['tracking_number'] ?? null,
            'shipping_address' => $data['shipping_address'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
        if ($delivery->status === 'shipped' && !$delivery->shipped_at) { $delivery->shipped_at = now(); $delivery->save(); }
        if ($delivery->status === 'delivered' && !$delivery->delivered_at) { $delivery->delivered_at = now(); $delivery->save(); }
        return redirect()->route('deliveries.show',$delivery)->with('success','Delivery updated');
    }

    public function destroy(Delivery $delivery)
    {
        $this->d();
        $delivery->delete();
        return redirect()->route('deliveries.index')->with('success','Delivery deleted');
    }

    public function ship(Delivery $delivery)
    {
        $this->e();
        if ($delivery->batch && $delivery->batch->qc_status !== 'passed') {
            return back()->with('error','Cannot ship: batch not passed QC');
        }
        $delivery->update(['status' => 'shipped', 'shipped_at' => now()]);
        return back()->with('success','Marked as shipped');
    }

    public function markDelivered(Delivery $delivery)
    {
        $this->e();
        $delivery->update(['status' => 'delivered', 'delivered_at' => now()]);
        return back()->with('success','Marked as delivered');
    }

    public function markReturned(Delivery $delivery)
    {
        $this->e();
        $delivery->update(['status' => 'returned']);
        return back()->with('success','Marked as returned');
    }

    public function markRejected(Delivery $delivery)
    {
        $this->e();
        $delivery->update(['status' => 'rejected']);
        return back()->with('success','Marked as rejected');
    }

    private function v(){ abort_unless(Gate::allows('permission','delivery.view'),403);} private function c(){ abort_unless(Gate::allows('permission','delivery.create'),403);} private function e(){ abort_unless(Gate::allows('permission','delivery.edit'),403);} private function d(){ abort_unless(Gate::allows('permission','delivery.delete'),403);} 
}
