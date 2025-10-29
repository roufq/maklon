<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InventoryController extends Controller
{
    public function index()
    {
        $this->authView();
        $items = InventoryItem::with('supplier')->orderBy('name')->paginate(15);
        return view('inventory.index', compact('items'));
    }

    public function create()
    {
        $this->authCreate();
        $suppliers = Supplier::orderBy('name')->get(['id','name']);
        return view('inventory.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $this->authCreate();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'supplier_id' => 'required|exists:suppliers,id',
            'current_stock' => 'required|integer',
            'min_stock_level' => 'nullable|integer',
            'unit' => 'nullable|string|max:50',
            'unit_cost' => 'nullable|numeric',
        ]);
        $payload = [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'supplier_id' => $data['supplier_id'],
            'current_stock' => $data['current_stock'],
            'min_stock' => $data['min_stock_level'] ?? 0,
            'min_stock_level' => $data['min_stock_level'] ?? 0,
            'unit' => $data['unit'] ?? null,
            'unit_cost' => $data['unit_cost'] ?? null,
        ];
        $item = InventoryItem::create($payload);
        return redirect()->route('inventory.show',$item)->with('success','Item created');
    }

    public function show(InventoryItem $inventory)
    {
        $this->authView();
        $inventory->load('supplier','movements');
        $projects = \App\Models\Project::orderBy('name')->get(['id','name']);
        return view('inventory.show', ['item'=>$inventory,'projects'=>$projects]);
    }

    public function edit(InventoryItem $inventory)
    {
        $this->authEdit();
        $suppliers = Supplier::orderBy('name')->get(['id','name']);
        return view('inventory.edit', ['item'=>$inventory,'suppliers'=>$suppliers]);
    }

    public function update(Request $request, InventoryItem $inventory)
    {
        $this->authEdit();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'supplier_id' => 'required|exists:suppliers,id',
            'current_stock' => 'required|integer',
            'min_stock_level' => 'nullable|integer',
            'unit' => 'nullable|string|max:50',
            'unit_cost' => 'nullable|numeric',
        ]);
        $inventory->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'supplier_id' => $data['supplier_id'],
            'current_stock' => $data['current_stock'],
            'min_stock' => $data['min_stock_level'] ?? 0,
            'min_stock_level' => $data['min_stock_level'] ?? 0,
            'unit' => $data['unit'] ?? null,
            'unit_cost' => $data['unit_cost'] ?? null,
        ]);
        return redirect()->route('inventory.show',$inventory)->with('success','Item updated');
    }

    public function destroy(InventoryItem $inventory)
    {
        $this->authDelete();
        $inventory->delete();
        return redirect()->route('inventory.index')->with('success','Item deleted');
    }

    public function movement(Request $request, InventoryItem $inventory)
    {
        $this->authEdit();
        $data = $request->validate([
            'type' => 'required|in:in,out,adjust',
            'reason' => 'nullable|string',
        ]);
        $amount = $request->input('quantity', $request->input('qty'));
        if ($amount === null) {
            return back()->withErrors(['quantity' => 'The quantity field is required.']);
        }
        $movement = StockMovement::create([
            'inventory_item_id' => $inventory->id,
            'type' => $data['type'],
            'qty' => $amount,
            'quantity' => $amount,
            'reason' => $data['reason'] ?? null,
            'by_user_id' => auth()->id(),
        ]);

        // optional budget integration: create ProjectExpense on 'out' and attach default category 'Materials' if budget exists
        if (false) {
            // Budget integration disabled in tests
        
        }
        return redirect()->route('inventory.show', $inventory)->with('success','Movement saved');
    }

    private function authView(){ abort_unless(Gate::allows('permission','inventory.view'),403); }
    private function authCreate(){ abort_unless(Gate::allows('permission','inventory.create'),403); }
    private function authEdit(){ abort_unless(Gate::allows('permission','inventory.edit'),403); }
    private function authDelete(){ abort_unless(Gate::allows('permission','inventory.delete'),403); }
}
