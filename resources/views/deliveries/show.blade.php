@extends('layouts.app')
@section('title','Delivery #' . $delivery->id)
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Delivery #{{ $delivery->id }}</h3>
    <div class="d-flex gap-2">
      @if(auth()->user()->can('permission','delivery.edit'))
        <a class="btn btn-outline-primary" href="{{ route('deliveries.edit',$delivery) }}">Edit</a>
        @if($delivery->status==='ready')
          <form method="post" action="{{ route('deliveries.ship',$delivery) }}">@csrf<button class="btn btn-outline-secondary">Mark Shipped</button></form>
        @endif
        @if($delivery->status==='shipped')
          <form method="post" action="{{ route('deliveries.delivered',$delivery) }}">@csrf<button class="btn btn-success">Mark Delivered</button></form>
        @endif
        @if(in_array($delivery->status,['ready','shipped']))
          <form method="post" action="{{ route('deliveries.returned',$delivery) }}">@csrf<button class="btn btn-warning">Mark Returned</button></form>
          <form method="post" action="{{ route('deliveries.rejected',$delivery) }}">@csrf<button class="btn btn-danger">Mark Rejected</button></form>
        @endif
      @endif
    </div>
  </div>
  <div class="card border-0 shadow-sm"><div class="card-body">
    <div class="row mb-3">
      <div class="col-md-6">
        <div><strong>Project:</strong> {{ $delivery->project->name ?? '-' }}</div>
        <div><strong>Batch:</strong> {{ $delivery->batch->batch_number ?? '-' }}</div>
        <div><strong>Customer:</strong> {{ $delivery->customer->name ?? '-' }}</div>
        <div><strong>Status:</strong> {{ ucfirst($delivery->status) }}</div>
      </div>
      <div class="col-md-6">
        <div><strong>Shipping:</strong> {{ $delivery->shipping_provider ?? '-' }} / {{ $delivery->tracking_number ?? '-' }}
          @if($delivery->tracking_url)
            <a class="ms-2" href="{{ $delivery->tracking_url }}" target="_blank" rel="noopener">Track</a>
          @endif
        </div>
        <div><strong>Shipped:</strong> {{ optional($delivery->shipped_at)->format('Y-m-d H:i') ?: '-' }}</div>
        <div><strong>Delivered:</strong> {{ optional($delivery->delivered_at)->format('Y-m-d H:i') ?: '-' }}</div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-8">
        <div class="p-2 bg-light border rounded">
          <div><strong>Ship To:</strong> {{ $delivery->shipping_address['name'] ?? '-' }}</div>
          <div>{{ $delivery->shipping_address['address'] ?? '' }}</div>
          <div>{{ $delivery->shipping_address['city'] ?? '' }} {{ $delivery->shipping_address['zip'] ?? '' }}</div>
          <div>{{ $delivery->shipping_address['country'] ?? '' }} / {{ $delivery->shipping_address['phone'] ?? '' }}</div>
        </div>
      </div>
      <div class="col-md-4">
        <div><strong>Notes:</strong> {{ $delivery->notes ?? '-' }}</div>
      </div>
    </div>
  </div></div>
</div>
@endsection
