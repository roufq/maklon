@extends('layouts.app')
@section('title','Invoice ' . $invoice->number)
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Invoice {{ $invoice->number }}</h3>
    <span class="badge bg-{{ $invoice->status==='paid' ? 'success' : ($invoice->status==='sent' ? 'primary' : 'secondary') }}">{{ ucfirst($invoice->status) }}</span>
  </div>
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-6">
          <div><strong>Project:</strong> {{ $invoice->project->name ?? 'N/A' }}</div>
          @if(optional($invoice->project?->bpomRegistration)->registration_number)
          <div><strong>BPOM Reg:</strong> {{ $invoice->project->bpomRegistration->registration_number }} ({{ ucfirst($invoice->project->bpomRegistration->status) }})</div>
          @endif
          <div><strong>Client:</strong> {{ $invoice->client_name ?? '-' }}</div>
        </div>
        <div class="col-md-6">
          <div><strong>Issue Date:</strong> {{ $invoice->issue_date->format('Y-m-d') }}</div>
          <div><strong>Due Date:</strong> {{ optional($invoice->due_date)->format('Y-m-d') ?? '-' }}</div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead class="table-light">
            <tr><th>Description</th><th>Qty</th><th>Unit Price</th><th>Amount</th></tr>
          </thead>
          <tbody>
            @foreach($invoice->items as $it)
              <tr>
                <td>{{ $it->description }}</td>
                <td>{{ number_format($it->quantity,2) }}</td>
                <td>Rp {{ number_format($it->unit_price,0,',','.') }}</td>
                <td>Rp {{ number_format($it->amount,0,',','.') }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr><th colspan="3" class="text-end">Subtotal</th><th>Rp {{ number_format($invoice->subtotal,0,',','.') }}</th></tr>
            <tr><th colspan="3" class="text-end">Tax ({{ number_format($invoice->tax_percent,2) }}%)</th><th>Rp {{ number_format($invoice->tax_amount,0,',','.') }}</th></tr>
            <tr><th colspan="3" class="text-end">Total</th><th>Rp {{ number_format($invoice->total_amount,0,',','.') }}</th></tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
