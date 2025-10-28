@extends('layouts.app')
@section('title','Inventory Health')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Inventory Health</h3>
  </div>
  <form class="card card-body mb-3" method="get" action="{{ route('reports.inventory') }}">
    <div class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Turnover Window (days)</label>
        <input type="number" class="form-control" name="days" value="{{ $days }}" min="1">
      </div>
      <div class="col-md-2">
        <button class="btn btn-primary">Apply</button>
      </div>
    </div>
  </form>

  <div class="card">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead class="table-light">
          <tr>
            <th>Item</th>
            <th>Supplier</th>
            <th>Stock</th>
            <th>Min Stock</th>
            <th>Out ({{ $days }}d)</th>
            <th>Avg Daily Out</th>
            <th>Est. Days of Stock</th>
            <th>Low?</th>
          </tr>
        </thead>
        <tbody>
          @forelse($summary as $row)
            <tr>
              <td>{{ $row['item']->name }}</td>
              <td>{{ $row['item']->supplier->name ?? '-' }}</td>
              <td>{{ $row['item']->current_stock }}</td>
              <td>{{ $row['item']->min_stock }}</td>
              <td>{{ $row['outQty'] }}</td>
              <td>{{ $row['avgDaily'] }}</td>
              <td>{{ $row['daysOfStock'] ?? '-' }}</td>
              <td>
                @if($row['isLow'])
                  <span class="badge bg-danger">Yes</span>
                @else
                  <span class="badge bg-success">No</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center text-muted">No data</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

