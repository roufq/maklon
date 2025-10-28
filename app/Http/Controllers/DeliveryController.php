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
    public function index()
    {
        $this->v();
        $deliveries = Delivery::with(['project','batch','customer'])->latest()->paginate(15);
        return view('deliveries.index', compact('deliveries'));
    }

    public function create()
    {
        $this->c();
        $projects = Project::orderBy('name')->get(['id','name']);
        $batches = ProductionBatch::where('qc_status','passed')->orderByDesc('id')->get(['id','batch_number']);
        $customers = User::role('Client')->orderBy('name')->get(['id','name']);
        return view('deliveries.create', compact('projects','batches','customers'));
    }

    public function store(Request $request)
    {
        $this->c();
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'production_batch_id' => 'nullable|exists:production_batches,id',
            'customer_id' => 'nullable|exists:users,id',
            'shipping_provider' => 'nullable|string',
            'tracking_number' => 'nullable|string',
            'shipping_name' => 'nullable|string',
            'shipping_address' => 'nullable|string',
            'shipping_city' => 'nullable|string',
            'shipping_zip' => 'nullable|string',
            'shipping_country' => 'nullable|string',
            'shipping_phone' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        // QC gating: if batch selected, must be passed
        if (!empty($data['production_batch_id'])) {
            $batch = ProductionBatch::findOrFail($data['production_batch_id']);
            if ($batch->qc_status !== 'passed') {
                return back()->with('error','Batch not passed QC')->withInput();
            }
        }
        $shippingAddress = [
            'name' => $data['shipping_name'] ?? null,
            'address' => $data['shipping_address'] ?? null,
            'city' => $data['shipping_city'] ?? null,
            'zip' => $data['shipping_zip'] ?? null,
            'country' => $data['shipping_country'] ?? null,
            'phone' => $data['shipping_phone'] ?? null,
        ];
        $delivery = Delivery::create([
            'project_id' => $data['project_id'],
            'production_batch_id' => $data['production_batch_id'] ?? null,
            'customer_id' => $data['customer_id'] ?? null,
            'status' => 'ready',
            'shipping_provider' => $data['shipping_provider'] ?? null,
            'tracking_number' => $data['tracking_number'] ?? null,
            'shipping_address' => $shippingAddress,
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
        $customers = User::role('Client')->orderBy('name')->get(['id','name']);
        return view('deliveries.edit', compact('delivery','projects','batches','customers'));
    }

    public function update(Request $request, Delivery $delivery)
    {
        $this->e();
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'production_batch_id' => 'nullable|exists:production_batches,id',
            'customer_id' => 'nullable|exists:users,id',
            'status' => 'required|in:draft,ready,shipped,delivered,returned,rejected',
            'shipping_provider' => 'nullable|string',
            'tracking_number' => 'nullable|string',
            'shipping_name' => 'nullable|string',
            'shipping_address' => 'nullable|string',
            'shipping_city' => 'nullable|string',
            'shipping_zip' => 'nullable|string',
            'shipping_country' => 'nullable|string',
            'shipping_phone' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        if (!empty($data['production_batch_id'])) {
            $batch = ProductionBatch::findOrFail($data['production_batch_id']);
            if ($batch->qc_status !== 'passed') {
                return back()->with('error','Batch not passed QC')->withInput();
            }
        }
        $shippingAddress = [
            'name' => $data['shipping_name'] ?? null,
            'address' => $data['shipping_address'] ?? null,
            'city' => $data['shipping_city'] ?? null,
            'zip' => $data['shipping_zip'] ?? null,
            'country' => $data['shipping_country'] ?? null,
            'phone' => $data['shipping_phone'] ?? null,
        ];
        $delivery->update([
            'project_id' => $data['project_id'],
            'production_batch_id' => $data['production_batch_id'] ?? null,
            'customer_id' => $data['customer_id'] ?? null,
            'status' => $data['status'],
            'shipping_provider' => $data['shipping_provider'] ?? null,
            'tracking_number' => $data['tracking_number'] ?? null,
            'shipping_address' => $shippingAddress,
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


