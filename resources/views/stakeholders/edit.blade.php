@extends('layouts.app')
@section('title','Edit Stakeholder')
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">Edit Stakeholder</h3>
  <form method="post" action="{{ route('stakeholders.update',$stakeholder) }}" class="card p-3 border-0 shadow-sm">
    @csrf @method('PUT')
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Project</label>
        <select name="project_id" class="form-select" required>
          @foreach($projects as $p)
            <option value="{{ $p->id }}" {{ $stakeholder->project_id==$p->id?'selected':'' }}>{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="{{ $stakeholder->name }}" required />
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ $stakeholder->email }}" />
      </div>
      <div class="col-md-6">
        <label class="form-label">Role</label>
        <input type="text" name="role" class="form-control" value="{{ $stakeholder->role }}" />
      </div>
      <div class="col-md-6">
        <label class="form-label">Influence</label>
        <select name="influence_level" class="form-select">
          @foreach(['very_low','low','medium','high','very_high'] as $lvl)
            <option value="{{ $lvl }}" {{ $stakeholder->influence_level==$lvl?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$lvl)) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Interest</label>
        <select name="interest_level" class="form-select">
          @foreach(['very_low','low','medium','high','very_high'] as $lvl)
            <option value="{{ $lvl }}" {{ $stakeholder->interest_level==$lvl?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$lvl)) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-12">
        <label class="form-label">Communication Plan</label>
        <textarea name="communication_plan" class="form-control" rows="4">{{ $stakeholder->communication_plan }}</textarea>
      </div>
    </div>
    <div class="mt-3 d-flex gap-2">
      <button class="btn btn-primary">Update</button>
      <a class="btn btn-outline-secondary" href="{{ route('stakeholders.show',$stakeholder) }}">Cancel</a>
      <form method="post" action="{{ route('stakeholders.destroy',$stakeholder) }}" onsubmit="return confirm('Delete stakeholder?')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger">Delete</button>
      </form>
    </div>
  </form>
</div>
@endsection

