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
            'supplier_id' => 'nullable|exists:suppliers,id',
            'current_stock' => 'nullable|integer',
            'min_stock' => 'nullable|integer',
            'unit' => 'nullable|string|max:50',
            'unit_cost' => 'nullable|numeric',
        ]);
        $item = InventoryItem::create($data);
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
            'supplier_id' => 'nullable|exists:suppliers,id',
            'current_stock' => 'nullable|integer',
            'min_stock' => 'nullable|integer',
            'unit' => 'nullable|string|max:50',
            'unit_cost' => 'nullable|numeric',
        ]);
        $inventory->update($data);
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
            'qty' => 'required|integer',
            'project_id' => 'nullable|exists:projects,id',
        ]);
        $movement = StockMovement::create([
            'inventory_item_id' => $inventory->id,
            'type' => $data['type'],
            'qty' => $data['qty'],
            'by_user_id' => auth()->id(),
            'reference_type' => $data['project_id'] ? \App\Models\Project::class : null,
            'reference_id' => $data['project_id'] ?? null,
        ]);
        // update stock
        $delta = $data['type'] === 'out' ? -$data['qty'] : $data['qty'];
        if ($data['type'] === 'adjust') { $delta = $data['qty']; }
        $inventory->update(['current_stock' => $inventory->current_stock + $delta]);

        // optional budget integration: create ProjectExpense on 'out' and attach default category 'Materials' if budget exists
        if ($data['type'] === 'out' && $data['project_id'] && $inventory->unit_cost !== null) {
            $projectId = (int) $data['project_id'];
            $amount = round((float)$inventory->unit_cost * (int)$data['qty'], 2);

            $budget = \App\Models\ProjectBudget::where('project_id', $projectId)->first();
            $categoryId = null;
            if ($budget) {
                $category = \App\Models\BudgetCategory::firstOrCreate(
                    ['project_budget_id' => $budget->id, 'name' => 'Materials'],
                    ['allocated_amount' => 0, 'spent_amount' => 0]
                );
                $categoryId = $category->id;
            }

            $expense = \App\Models\ProjectExpense::create([
                'project_id' => $projectId,
                'budget_category_id' => $categoryId,
                'entered_by' => auth()->id(),
                'amount' => $amount,
                'spent_at' => now()->toDateString(),
                'billable' => false,
                'currency' => config('finance.currency','IDR'),
                'description' => 'Inventory usage: '.$inventory->name.' (Movement #'.$movement->id.')',
            ]);

            // increment category and budget spent if available
            if (isset($category) && $category) {
                $category->spent_amount = ($category->spent_amount + $amount);
                $category->save();
            }
            if ($budget) {
                $budget->spent_amount = ($budget->spent_amount + $amount);
                $budget->save();
            }
        }
        return back()->with('success','Movement saved');
    }

    private function authView(){ abort_unless(Gate::allows('permission','inventory.view'),403); }
    private function authCreate(){ abort_unless(Gate::allows('permission','inventory.create'),403); }
    private function authEdit(){ abort_unless(Gate::allows('permission','inventory.edit'),403); }
    private function authDelete(){ abort_unless(Gate::allows('permission','inventory.delete'),403); }
}


