<?php

namespace App\Http\Controllers;

use App\Models\BoxType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BoxTypeController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('permission','boxes.view'),403);
        $types = BoxType::orderBy('name')->paginate(15);
        return view('box_types.index', compact('types'));
    }

    public function create()
    {
        abort_unless(Gate::allows('permission','boxes.create'),403);
        return view('box_types.create');
    }

    public function store(Request $request)
    {
        abort_unless(Gate::allows('permission','boxes.create'),403);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        BoxType::create($data);
        return redirect()->route('box-types.index')->with('success','Box Type created');
    }

    public function edit(BoxType $boxType)
    {
        abort_unless(Gate::allows('permission','boxes.edit'),403);
        return view('box_types.edit', compact('boxType'));
    }

    public function update(Request $request, BoxType $boxType)
    {
        abort_unless(Gate::allows('permission','boxes.edit'),403);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $boxType->update($data);
        return redirect()->route('box-types.index')->with('success','Box Type updated');
    }

    public function destroy(BoxType $boxType)
    {
        abort_unless(Gate::allows('permission','boxes.delete'),403);
        $boxType->delete();
        return redirect()->route('box-types.index')->with('success','Box Type deleted');
    }
}
