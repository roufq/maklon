<?php

namespace App\Http\Controllers;

use App\Models\WorkStation;
use Illuminate\Http\Request;

class WorkStationController extends Controller
{
    public function index()
    {
        $this->authorizeView();
        $items = WorkStation::orderBy('name')->paginate(15);
        return view('work_stations.index', compact('items'));
    }

    public function create()
    {
        $this->authorizeCreate();
        return view('work_stations.create');
    }

    public function store(Request $request)
    {
        $this->authorizeCreate();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'capacity_per_hour' => 'required|integer|min:0',
            'status' => 'required|in:active,maintenance,offline',
        ]);
        WorkStation::create($data);
        return redirect()->route('work-stations.index')->with('success','Work Station created');
    }

    public function show(WorkStation $work_station)
    {
        $this->authorizeView();
        return view('work_stations.show', ['item' => $work_station]);
    }

    public function edit(WorkStation $work_station)
    {
        $this->authorizeEdit();
        return view('work_stations.edit', ['item' => $work_station]);
    }

    public function update(Request $request, WorkStation $work_station)
    {
        $this->authorizeEdit();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'capacity_per_hour' => 'required|integer|min:0',
            'status' => 'required|in:active,maintenance,offline',
        ]);
        $work_station->update($data);
        return redirect()->route('work-stations.show', $work_station)->with('success','Work Station updated');
    }

    public function destroy(WorkStation $work_station)
    {
        $this->authorizeDelete();
        $work_station->delete();
        return redirect()->route('work-stations.index')->with('success','Work Station deleted');
    }

    private function authorizeView(): void
    {
        abort_unless(auth()->user()?->can('production.view'), 403);
    }
    private function authorizeCreate(): void
    {
        abort_unless(auth()->user()?->can('production.create'), 403);
    }
    private function authorizeEdit(): void
    {
        abort_unless(auth()->user()?->can('production.edit'), 403);
    }
    private function authorizeDelete(): void
    {
        abort_unless(auth()->user()?->can('production.delete'), 403);
    }
}

