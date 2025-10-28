@extends('layouts.app')
@section('title','Invoice ' . $invoice->number)
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Invoice {{ $invoice->number }}</h3>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary" href="{{ route('invoices.pdf',$invoice) }}">PDF</a>
      @if($invoice->status==='draft')
        @can('invoices.approve')
        <form method="post" action="{{ route('invoices.approve',$invoice) }}">@csrf<button class="btn btn-primary">Approve & Send</button></form>
        @endcan
      @elseif($invoice->status==='sent')
        @can('invoices.edit')
        <form method="post" action="{{ route('invoices.paid',$invoice) }}">@csrf<button class="btn btn-success">Mark Paid</button></form>
        @endcan
      @endif
    </div>
  </div>
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-6">
          <div><strong>Project:</strong> {{ $invoice->project->name ?? 'N/A' }}</div>
          <div><strong>Client:</strong> {{ $invoice->client_name ?? '-' }} ({{ $invoice->client_email ?? '-' }})</div>
        </div>
        <div class="col-md-6">
          <div><strong>Issue Date:</strong> {{ $invoice->issue_date->format('Y-m-d') }}</div>
          <div><strong>Due Date:</strong> {{ optional($invoice->due_date)->format('Y-m-d') ?? '-' }}</div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead class="table-light">
            <tr><th>Description</th><th>Qty</th><th>Unit Price</th><th>Amount</th><th></th></tr>
          </thead>
          <tbody>
            @forelse($invoice->items as $it)
              <tr>
                <td>{{ $it->description }}</td>
                <td>{{ number_format($it->quantity,2) }}</td>
                <td>Rp {{ number_format($it->unit_price,0,',','.') }}</td>
                <td>Rp {{ number_format($it->amount,0,',','.') }}</td>
                <td>
                  <form method="post" action="{{ route('invoices.items.delete', [$invoice, $it]) }}" onsubmit="return confirm('Delete item?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center py-4">No items</td></tr>
            @endforelse
          </tbody>
          <tfoot>
            <tr><th colspan="3" class="text-end">Subtotal</th><th>Rp {{ number_format($invoice->subtotal,0,',','.') }}</th></tr>
            <tr><th colspan="3" class="text-end">Tax ({{ number_format($invoice->tax_percent,2) }}%)</th><th>Rp {{ number_format($invoice->tax_amount,0,',','.') }}</th></tr>
            <tr><th colspan="3" class="text-end">Total</th><th>Rp {{ number_format($invoice->total_amount,0,',','.') }}</th></tr>
          </tfoot>
        </table>
      </div>
      <div class="mt-3">
        <form class="row g-2" method="post" action="{{ route('invoices.items.add', $invoice) }}">
          @csrf
          <div class="col-md-6"><input class="form-control" name="description" placeholder="Description" required></div>
          <div class="col-md-2"><input class="form-control" type="number" step="0.01" name="quantity" placeholder="Qty" required></div>
          <div class="col-md-2"><input class="form-control" type="number" step="0.01" name="unit_price" placeholder="Unit Price" required></div>
          <div class="col-md-2"><button class="btn btn-outline-primary w-100">Add Item</button></div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
