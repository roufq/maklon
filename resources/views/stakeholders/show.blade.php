@extends('layouts.app')
@section('title','Stakeholder')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">{{ $stakeholder->name }}</h3>
    <a class="btn btn-outline-primary" href="{{ route('stakeholders.edit',$stakeholder) }}">Edit</a>
  </div>
  <div class="row g-3">
    <div class="col-md-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div><strong>Project:</strong> {{ $stakeholder->project->name ?? 'N/A' }}</div>
          <div><strong>Email:</strong> {{ $stakeholder->email }}</div>
          <div><strong>Role:</strong> {{ $stakeholder->role }}</div>
          <div><strong>Influence:</strong> {{ ucfirst(str_replace('_',' ',$stakeholder->influence_level)) }}</div>
          <div><strong>Interest:</strong> {{ ucfirst(str_replace('_',' ',$stakeholder->interest_level)) }}</div>
          <div class="mt-2"><strong>Communication Plan:</strong><br>{!! nl2br(e($stakeholder->communication_plan)) !!}</div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-0">Comms Log</div>
        <div class="card-body">
          <form method="post" action="{{ route('stakeholders.comms.store',$stakeholder) }}" class="row g-2 mb-3">
            @csrf
            <div class="col-12 col-md-5"><input class="form-control" name="subject" placeholder="Subject" required></div>
            <div class="col-12 col-md-4"><input class="form-control" type="datetime-local" name="planned_at"></div>
            <div class="col-12 col-md-3"><button class="btn btn-primary w-100">Add</button></div>
            <div class="col-12"><textarea class="form-control" rows="2" name="notes" placeholder="Notes"></textarea></div>
          </form>
          <ul class="list-group list-group-flush">
            @forelse($stakeholder->comms()->latest()->get() as $c)
              <li class="list-group-item">
                <div class="d-flex justify-content-between">
                  <div><strong>{{ $c->subject }}</strong><br><small class="text-muted">Planned: {{ optional($c->planned_at)->format('Y-m-d H:i') ?? '-' }} | Sent: {{ optional($c->sent_at)->format('Y-m-d H:i') ?? '-' }}</small></div>
                  <div class="text-muted">{{ $c->created_at->diffForHumans() }}</div>
                </div>
                @if($c->notes)<div class="mt-1 small">{{ $c->notes }}</div>@endif
              </li>
            @empty
              <li class="list-group-item text-center text-muted">No communications yet</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

