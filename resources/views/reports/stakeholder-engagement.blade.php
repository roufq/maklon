@extends('layouts.app')
@section('title','Stakeholder Engagement')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Stakeholder Engagement</h3>
  </div>
  <form class="row g-2 mb-3" method="get">
    <div class="col-md-4">
      <select name="project_id" class="form-select" onchange="this.form.submit()">
        <option value="">All Projects</option>
        @foreach($projects as $p)
          <option value="{{ $p->id }}" {{ (string)$projectId===(string)$p->id? 'selected':'' }}>{{ $p->name }}</option>
        @endforeach
      </select>
    </div>
  </form>
  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead class="table-light"><tr><th>Survey</th><th>Project</th><th>Responses</th><th>Engagement</th><th>Avg Rating</th></tr></thead>
        <tbody>
          @forelse($summary as $row)
          <tr>
            <td>{{ $row['survey']->title }}</td>
            <td>{{ $row['survey']->project->name ?? 'N/A' }}</td>
            <td>{{ $row['responses'] }} / {{ $row['total'] }}</td>
            <td>{{ $row['engagement_rate'] }}%</td>
            <td>{{ $row['avg_rating'] }}</td>
          </tr>
          @empty
          <tr><td colspan="5" class="text-center py-4">No surveys</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

