@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold">Budget: {{ $budget->project->name }}</h1>
        <a href="{{ route('budgets.index') }}" class="btn">Back</a>
    </div>

    <div class="bg-white shadow rounded p-4">
        <div class="grid grid-cols-6 gap-4">
            <div><div class="text-gray-500">Currency</div><div class="font-semibold">{{ $budget->currency }}</div></div>
            <div><div class="text-gray-500">Total</div><div class="font-semibold">{{ number_format($budget->total_budget, 2) }}</div></div>
            <div><div class="text-gray-500">Spent</div><div class="font-semibold">{{ number_format($budget->spent_amount, 2) }}</div></div>
            <div><div class="text-gray-500">Variance</div><div class="font-semibold">{{ number_format($budget->variance, 2) }}</div></div>
            <div><div class="text-gray-500">Utilization</div><div class="font-semibold">{{ number_format($budget->utilization_percent, 1) }}%</div></div>
            <div>
                <div class="text-gray-500">Status</div>
                <div class="font-semibold">{{ ucfirst($budget->status ?? 'draft') }}</div>
                @if(($budget->status ?? 'draft') === 'draft')
                    <form method="POST" action="{{ route('budgets.approve', $budget) }}">
                        @csrf
                        <button class="btn btn-sm btn-success mt-1">Approve</button>
                    </form>
                @else
                    <div class="text-xs text-gray-500">Approved at {{ optional($budget->approved_at)->format('Y-m-d H:i') }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-lg font-semibold mb-3">Categories</h2>
            <form method="POST" action="{{ route('budgets.categories.store', $budget) }}" class="mb-4 grid grid-cols-3 gap-2">
                @csrf
                <input name="name" placeholder="Category name" class="form-input" required />
                <input type="number" name="allocated_amount" step="0.01" placeholder="Allocated" class="form-input" required />
                <button class="btn btn-primary">Add</button>
            </form>
            <table class="table-auto w-full">
                <thead><tr><th class="text-left px-2 py-1">Name</th><th class="text-right px-2 py-1">Allocated</th><th class="text-right px-2 py-1">Spent</th></tr></thead>
                <tbody>
                    @foreach($budget->categories as $c)
                        <tr class="border-t">
                            <td class="px-2 py-1">{{ $c->name }}</td>
                            <td class="px-2 py-1 text-right">{{ number_format($c->allocated_amount, 2) }}</td>
                            <td class="px-2 py-1 text-right">{{ number_format($c->spent_amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="bg-white shadow rounded p-4">
            <h2 class="text-lg font-semibold mb-3">Record Expense</h2>
            <form method="POST" action="{{ route('budgets.expenses.store', $budget) }}" class="space-y-2">
                @csrf
                <div>
                    <label class="block mb-1">Category</label>
                    <select name="budget_category_id" class="form-select w-full">
                        <option value="">-- None --</option>
                        @foreach($budget->categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block mb-1">Amount</label>
                        <input type="number" step="0.01" name="amount" class="form-input w-full" required />
                    </div>
                    <div>
                        <label class="block mb-1">Date</label>
                        <input type="date" name="spent_at" class="form-input w-full" required />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block mb-1">Billable</label>
                        <input type="checkbox" name="billable" value="1" />
                    </div>
                    <div>
                        <label class="block mb-1">Currency</label>
                        <input type="text" name="currency" value="{{ $budget->currency }}" class="form-input w-full" />
                    </div>
                </div>
                <div>
                    <label class="block mb-1">Description</label>
                    <textarea name="description" class="form-textarea w-full" rows="2"></textarea>
                </div>
                <button class="btn btn-primary">Save Expense</button>
            </form>
        </div>
    </div>
    <div class="text-sm text-gray-500">Alerts trigger at {{ $budget->threshold_warning_percent }}% and {{ $budget->threshold_critical_percent }}% utilization.</div>
</div>
@endsection
