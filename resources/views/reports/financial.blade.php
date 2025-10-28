@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 space-y-6">
    <h1 class="text-2xl font-semibold">Financial Report</h1>

    <div class="bg-white shadow rounded p-4">
        <div class="grid grid-cols-3 gap-4">
            <div><div class="text-gray-500">Avg Daily Spend (30d)</div><div class="font-semibold">{{ number_format($avgDailySpend, 2) }}</div></div>
            <div><div class="text-gray-500">Forecast Remainder (this month)</div><div class="font-semibold">{{ number_format($forecastRemainder, 2) }}</div></div>
        </div>
    </div>

    <div class="bg-white shadow rounded">
        <table class="table-auto w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Project</th>
                    <th class="px-4 py-2 text-right">Total Budget</th>
                    <th class="px-4 py-2 text-right">Spent</th>
                    <th class="px-4 py-2 text-right">Variance</th>
                    <th class="px-4 py-2 text-right">Utilization</th>
                </tr>
            </thead>
            <tbody>
                @foreach($budgets as $row)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $row['project'] }}</td>
                        <td class="px-4 py-2 text-right">{{ $row['currency'] }} {{ number_format($row['total_budget'], 2) }}</td>
                        <td class="px-4 py-2 text-right">{{ $row['currency'] }} {{ number_format($row['spent'], 2) }}</td>
                        <td class="px-4 py-2 text-right">{{ $row['currency'] }} {{ number_format($row['variance'], 2) }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($row['utilization_percent'], 1) }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

