@extends('layouts.app')
@section('title','Power/Interest Matrix')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Power/Interest Matrix</h3>
    <form method="get" class="d-flex gap-2">
      <select class="form-select" name="project_id" onchange="this.form.submit()">
        <option value="">All Projects</option>
        @foreach($projects as $p)
          <option value="{{ $p->id }}" {{ (string)$projectId === (string)$p->id ? 'selected' : '' }}>{{ $p->name }}</option>
        @endforeach
      </select>
    </form>
  </div>
  <div class="row g-3">
    @php
      $by = fn($inf, $int) => $stakeholders->filter(fn($s)=>$s->influence_level==$inf && $s->interest_level==$int);
      $levels=['low','medium','high','very_high'];
    @endphp
    @foreach(['low','medium','high','very_high'] as $inf)
      @foreach(['low','medium','high','very_high'] as $int)
        <div class="col-md-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
              <strong>Power: {{ ucfirst(str_replace('_',' ',$inf)) }} / Interest: {{ ucfirst(str_replace('_',' ',$int)) }}</strong>
            </div>
            <div class="card-body">
              <ul class="list-unstyled mb-0">
                @forelse($by($inf,$int) as $s)
                  <li class="mb-1"><a href="{{ route('stakeholders.show',$s) }}">{{ $s->name }}</a></li>
                @empty
                  <li class="text-muted">-</li>
                @endforelse
              </ul>
            </div>
          </div>
        </div>
      @endforeach
    @endforeach
  </div>
</div>
@endsection

