@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold">Project Budgets</h1>
        @can('budgets.create')
        <a href="{{ route('budgets.create') }}" class="btn btn-primary">Create Budget</a>
        @endcan
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-white shadow rounded">
        <table class="table-auto w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Project</th>
                    <th class="px-4 py-2 text-right">Total</th>
                    <th class="px-4 py-2 text-right">Spent</th>
                    <th class="px-4 py-2 text-right">Variance</th>
                    <th class="px-4 py-2 text-right">Utilization</th>
                    <th class="px-4 py-2">Action</th>
                </tr>
            </thead>
            <tbody>
            @foreach($budgets as $b)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $b->project->name }}</td>
                    <td class="px-4 py-2 text-right">{{ $b->currency }} {{ number_format($b->total_budget, 2) }}</td>
                    <td class="px-4 py-2 text-right">{{ $b->currency }} {{ number_format($b->spent_amount, 2) }}</td>
                    <td class="px-4 py-2 text-right">{{ $b->currency }} {{ number_format($b->variance, 2) }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($b->utilization_percent, 1) }}%</td>
                    <td class="px-4 py-2 text-center">
                        <a class="btn btn-sm" href="{{ route('budgets.show', $b) }}">View</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $budgets->links() }}</div>
</div>
@endsection
