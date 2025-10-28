@extends('layouts.app')
@section('title','Stakeholder Surveys')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Stakeholder Surveys</h3>
    @can('projects.edit')<a class="btn btn-primary" href="{{ route('surveys.create') }}">New Survey</a>@endcan
  </div>
  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead class="table-light"><tr><th>Title</th><th>Project</th><th>Questions</th><th>Responses</th><th></th></tr></thead>
        <tbody>
          @forelse($surveys as $s)
            <tr>
              <td>{{ $s->title }}</td>
              <td>{{ $s->project->name ?? 'N/A' }}</td>
              <td>{{ count($s->questions) }}</td>
              <td>{{ $s->responses()->whereNotNull('submitted_at')->count() }}</td>
              <td><a class="btn btn-sm btn-outline-primary" href="{{ route('surveys.show',$s) }}">Open</a></td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center py-4">No surveys</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $surveys->links() }}</div>
  </div>
</div>
@endsection

