<?php

namespace App\Http\Controllers;

use App\Models\Bom;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BomController extends Controller
{
    public function index(){ $this->v(); $boms = Bom::orderBy('product_name')->paginate(15); return view('boms.index', compact('boms')); }
    public function create(){ $this->c(); $items = InventoryItem::orderBy('name')->get(['id','name']); return view('boms.create', compact('items')); }
    public function store(Request $r){ $this->c(); $d = $r->validate(['product_name'=>'required','version'=>'nullable','materials'=>'array']); $b = Bom::create(['product_name'=>$d['product_name'],'version'=>$d['version']??null,'materials'=>$d['materials']??[],'total_cost'=>null]); return redirect()->route('boms.show',$b); }
    public function show(Bom $bom){ $this->v(); return view('boms.show', compact('bom')); }
    public function edit(Bom $bom){ $this->e(); $items = InventoryItem::orderBy('name')->get(['id','name']); return view('boms.edit', compact('bom','items')); }
    public function update(Request $r, Bom $bom){ $this->e(); $d = $r->validate(['product_name'=>'required','version'=>'nullable','materials'=>'array']); $bom->update(['product_name'=>$d['product_name'],'version'=>$d['version']??null,'materials'=>$d['materials']??[]]); return redirect()->route('boms.show',$bom)->with('success','BOM updated'); }
    public function destroy(Bom $bom){ $this->d(); $bom->delete(); return redirect()->route('boms.index')->with('success','BOM deleted'); }
    private function v(){ abort_unless(Gate::allows('permission','inventory.view'),403);} private function c(){ abort_unless(Gate::allows('permission','inventory.create'),403);} private function e(){ abort_unless(Gate::allows('permission','inventory.edit'),403);} private function d(){ abort_unless(Gate::allows('permission','inventory.delete'),403);} 
}


