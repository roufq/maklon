@extends('layouts.app')
@section('title','Create Stakeholder')
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">Create Stakeholder</h3>
  <form method="post" action="{{ route('stakeholders.store') }}" class="card p-3 border-0 shadow-sm">
    @csrf
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Project</label>
        <select name="project_id" class="form-select" required>
          @foreach($projects as $p)
            <option value="{{ $p->id }}">{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" required />
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" />
      </div>
      <div class="col-md-6">
        <label class="form-label">Role</label>
        <input type="text" name="role" class="form-control" />
      </div>
      <div class="col-md-6">
        <label class="form-label">Influence</label>
        <select name="influence_level" class="form-select">
          @foreach(['very_low','low','medium','high','very_high'] as $lvl)
            <option value="{{ $lvl }}">{{ ucfirst(str_replace('_',' ',$lvl)) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Interest</label>
        <select name="interest_level" class="form-select">
          @foreach(['very_low','low','medium','high','very_high'] as $lvl)
            <option value="{{ $lvl }}">{{ ucfirst(str_replace('_',' ',$lvl)) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-12">
        <label class="form-label">Communication Plan</label>
        <textarea name="communication_plan" class="form-control" rows="4"></textarea>
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-primary">Save</button>
      <a class="btn btn-outline-secondary" href="{{ route('stakeholders.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection

