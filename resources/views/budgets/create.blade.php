@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Create Project Budget</h1>

    <form method="POST" action="{{ route('budgets.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block mb-1">Project</label>
            <select name="project_id" class="form-select w-full" required>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block mb-1">Total Budget</label>
            <input type="number" name="total_budget" step="0.01" min="0" class="form-input w-full" required />
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block mb-1">Currency</label>
                <input type="text" name="currency" value="IDR" class="form-input w-full" />
            </div>
            <div>
                <label class="block mb-1">Warn %</label>
                <input type="number" name="threshold_warning_percent" value="80" min="0" max="100" class="form-input w-full" />
            </div>
            <div>
                <label class="block mb-1">Critical %</label>
                <input type="number" name="threshold_critical_percent" value="100" min="0" max="100" class="form-input w-full" />
            </div>
        </div>

        <button class="btn btn-primary">Save</button>
    </form>
</div>
@endsection

