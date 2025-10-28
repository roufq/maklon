@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 space-y-6">
    <h1 class="text-2xl font-semibold">Team Capacity Dashboard</h1>

    <form method="GET" class="bg-white shadow rounded p-4 grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
            <label class="block mb-1">Team</label>
            <select name="team_id" class="form-select w-full">
                <option value="">-- Select Team --</option>
                @foreach($teams as $team)
                    <option value="{{ $team->id }}" @if(optional($selectedTeam)->id === $team->id) selected @endif>{{ $team->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block mb-1">Start</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="form-input w-full" />
        </div>
        <div>
            <label class="block mb-1">End</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="form-input w-full" />
        </div>
        <div class="flex items-end">
            <label class="inline-flex items-center">
                <input type="checkbox" name="exclude_weekends" value="1" class="form-checkbox mr-2" {{ ($excludeWeekends ?? false) ? 'checked' : '' }} />
                Exclude weekends
            </label>
        </div>
        <div class="flex items-end">
            <button class="btn btn-primary w-full">Apply</button>
        </div>
        <div class="md:col-span-4 text-sm text-gray-500">Utilization assumes 8h/day across calendar days. Consider excluding weekends in future iteration.</div>
    </form>

    @if($selectedTeam)
    <div class="bg-white shadow rounded p-4">
        <h2 class="text-lg font-semibold mb-3">Utilization by Member</h2>
        <table class="table-auto w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Member</th>
                    <th class="px-4 py-2 text-right">Utilization %</th>
                </tr>
            </thead>
            <tbody>
                @foreach($selectedTeam->members as $m)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $m->name }}</td>
                        <td class="px-4 py-2 text-right">
                            {{ number_format($utilization[$m->id] ?? 0, 1) }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="bg-white shadow rounded p-4">
        <h2 class="text-lg font-semibold mb-3">Balancing Suggestions (MVP)</h2>
        @if(empty($suggestions))
            <div class="text-gray-500">No suggestions at this time.</div>
        @else
        <table class="table-auto w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Project</th>
                    <th class="px-4 py-2">From</th>
                    <th class="px-4 py-2">To</th>
                    <th class="px-4 py-2">Task</th>
                    <th class="px-4 py-2 text-right">Proposed Hours</th>
                    <th class="px-4 py-2">Dates</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suggestions as $s)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $s['project'] }}</td>
                        <td class="px-4 py-2">#{{ $s['from_user_id'] }}</td>
                        <td class="px-4 py-2">#{{ $s['to_user_id'] }}</td>
                        <td class="px-4 py-2">{{ $s['task'] ?? '-' }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($s['proposed_hours'], 2) }}</td>
                        <td class="px-4 py-2">{{ $s['date_range'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @endif
</div>
@endsection
