<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SupplierController extends Controller
{
    public function index()
    {
        $this->authorizeView();
        $suppliers = Supplier::orderBy('name')->paginate(15);
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $this->authorizeCreate();
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $this->authorizeCreate();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'bpom_certified' => 'boolean',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:100',
            'contact_address' => 'nullable|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);
        $supplier = Supplier::create([
            'name' => $data['name'],
            'type' => $data['type'] ?? null,
            'bpom_certified' => (bool)($data['bpom_certified'] ?? false),
            'contact_info' => [
                'email' => $data['contact_email'] ?? null,
                'phone' => $data['contact_phone'] ?? null,
                'address' => $data['contact_address'] ?? null,
            ],
            'rating' => $data['rating'] ?? null,
            'performance_score' => null,
        ]);
        return redirect()->route('suppliers.show', $supplier)->with('success','Supplier created');
    }

    public function show(Supplier $supplier)
    {
        $this->authorizeView();
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        $this->authorizeEdit();
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $this->authorizeEdit();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'bpom_certified' => 'boolean',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:100',
            'contact_address' => 'nullable|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'performance_score' => 'nullable|numeric|min:0',
        ]);
        $supplier->update([
            'name' => $data['name'],
            'type' => $data['type'] ?? null,
            'bpom_certified' => (bool)($data['bpom_certified'] ?? false),
            'contact_info' => [
                'email' => $data['contact_email'] ?? null,
                'phone' => $data['contact_phone'] ?? null,
                'address' => $data['contact_address'] ?? null,
            ],
            'rating' => $data['rating'] ?? null,
            'performance_score' => $data['performance_score'] ?? $supplier->performance_score,
        ]);
        return redirect()->route('suppliers.show', $supplier)->with('success','Supplier updated');
    }

    public function destroy(Supplier $supplier)
    {
        $this->authorizeDelete();
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success','Supplier deleted');
    }

    private function authorizeView(): void { abort_unless(auth()->user()?->can('supplier.view'), 403); }
    private function authorizeCreate(): void { abort_unless(auth()->user()?->can('supplier.create'), 403); }
    private function authorizeEdit(): void { abort_unless(auth()->user()?->can('supplier.edit'), 403); }
    private function authorizeDelete(): void { abort_unless(auth()->user()?->can('supplier.delete'), 403); }
}


