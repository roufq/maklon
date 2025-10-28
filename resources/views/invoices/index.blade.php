@extends('layouts.app')
@section('title','Invoices')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Invoices</h3>
    <a class="btn btn-primary" href="{{ route('invoices.create') }}">New Invoice</a>
  </div>
  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead class="table-light">
          <tr><th>Number</th><th>Project</th><th>Issue Date</th><th>Status</th><th>Total</th><th></th></tr>
        </thead>
        <tbody>
          @forelse($invoices as $inv)
            <tr>
              <td><a href="{{ route('invoices.show',$inv) }}">{{ $inv->number }}</a></td>
              <td>{{ $inv->project->name ?? 'N/A' }}</td>
              <td>{{ $inv->issue_date->format('Y-m-d') }}</td>
              <td><span class="badge bg-{{ $inv->status == 'paid' ? 'success' : ($inv->status=='sent'?'primary':'secondary') }}">{{ ucfirst($inv->status) }}</span></td>
              <td>Rp {{ number_format($inv->total_amount,0,',','.') }}</td>
              <td>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('invoices.pdf',$inv) }}">PDF</a>
                @if($inv->public_token)
                  <a class="btn btn-sm btn-outline-primary" href="{{ route('invoices.public',['token'=>$inv->public_token]) }}">Public Link</a>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center py-4">No invoices</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $invoices->links() }}</div>
  </div>
</div>
@endsection
