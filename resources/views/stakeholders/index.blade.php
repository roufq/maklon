@extends('layouts.app')
@section('title','Stakeholders')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Stakeholders</h3>
    <a class="btn btn-primary" href="{{ route('stakeholders.create') }}">New Stakeholder</a>
  </div>
  <form class="row g-2 mb-3" method="get">
    <div class="col-auto">
      <select class="form-select" name="project_id" onchange="this.form.submit()">
        <option value="">All Projects</option>
        @foreach($projects as $p)
          <option value="{{ $p->id }}" {{ (string)$projectId === (string)$p->id ? 'selected' : '' }}>{{ $p->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-auto">
      <a class="btn btn-outline-secondary" href="{{ route('stakeholders.matrix', ['project_id' => $projectId]) }}">Power/Interest Matrix</a>
    </div>
  </form>
  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead class="table-light">
          <tr>
            <th>Name</th><th>Project</th><th>Role</th><th>Influence</th><th>Interest</th><th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($stakeholders as $s)
            <tr>
              <td><a href="{{ route('stakeholders.show',$s) }}">{{ $s->name }}</a></td>
              <td>{{ $s->project->name ?? 'N/A' }}</td>
              <td>{{ $s->role }}</td>
              <td>{{ ucfirst(str_replace('_',' ',$s->influence_level)) }}</td>
              <td>{{ ucfirst(str_replace('_',' ',$s->interest_level)) }}</td>
              <td><a class="btn btn-sm btn-outline-primary" href="{{ route('stakeholders.edit',$s) }}">Edit</a></td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center py-4">No stakeholders</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $stakeholders->withQueryString()->links() }}</div>
  </div>
</div>
@endsection

