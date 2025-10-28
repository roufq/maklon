<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::with(['leader', 'members'])
                    ->latest()
                    ->paginate(10);

        return view('teams.index', compact('teams'));
    }

    public function create()
    {
        $users = User::all();
        return view('teams.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:project_team,department',
            'leader_id' => 'required|exists:users,id',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:users,id',
        ]);

        $team = Team::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'leader_id' => $validated['leader_id'],
        ]);

        if (isset($validated['member_ids'])) {
            $team->members()->attach($validated['member_ids']);
        }

        return redirect()->route('teams.index')
                        ->with('success', 'Team created successfully.');
    }

    public function show(Team $team)
    {
        $team->load(['leader', 'members', 'projects']);
        return view('teams.show', compact('team'));
    }

    public function edit(Team $team)
    {
        $users = User::all();
        $selectedMembers = $team->members->pluck('id')->toArray();

        return view('teams.edit', compact('team', 'users', 'selectedMembers'));
    }

    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:project_team,department',
            'leader_id' => 'required|exists:users,id',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:users,id',
        ]);

        $team->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'leader_id' => $validated['leader_id'],
        ]);

        if (isset($validated['member_ids'])) {
            $team->members()->sync($validated['member_ids']);
        } else {
            $team->members()->detach();
        }

        return redirect()->route('teams.index')
                        ->with('success', 'Team updated successfully.');
    }

    public function destroy(Team $team)
    {
        $team->members()->detach();
        $team->delete();

        return redirect()->route('teams.index')
                        ->with('success', 'Team deleted successfully.');
    }
}
