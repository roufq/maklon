@extends('layouts.app')
@section('title','Delivery Status Report')
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">Delivery Status Report</h3>
  <div class="row">
    <div class="col-md-6">
      <div class="card border-0 shadow-sm"><div class="card-body">
        <h6>Status Counts</h6>
        <ul class="mb-0">
          @foreach($byStatus as $status=>$count)
            <li>{{ ucfirst($status) }}: {{ $count }}</li>
          @endforeach
        </ul>
      </div></div>
    </div>
    <div class="col-md-6">
      <div class="card border-0 shadow-sm"><div class="card-body">
        <h6>Last 30 Days (created)</h6>
        <ul class="mb-0">
          @foreach($byDay as $day=>$count)
            <li>{{ $day }}: {{ $count }}</li>
          @endforeach
        </ul>
      </div></div>
    </div>
  </div>
</div>
@endsection

