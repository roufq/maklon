@extends('layouts.app')
@section('title','Survey: '.$survey->title)
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Survey: {{ $survey->title }}</h3>
    <a class="btn btn-outline-secondary" href="{{ route('surveys.index') }}">Back</a>
  </div>

  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <div><strong>Project:</strong> {{ $survey->project->name }}</div>
      <div><strong>Questions:</strong> {{ count($survey->questions) }}</div>
    </div>
  </div>

  <div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-0">Invite Stakeholder</div>
    <div class="card-body">
      <form method="post" action="{{ route('surveys.invite',$survey) }}" class="row g-2">
        @csrf
        <div class="col-md-6">
          <select class="form-select" name="stakeholder_id" required>
            @foreach($stakeholders as $s)
              <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->email }})</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Invite</button></div>
      </form>
      <div class="small text-muted mt-2">Mengundang akan membuat placeholder response. Link publik bisa ditambahkan nanti bila diperlukan.</div>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead class="table-light"><tr><th>Stakeholder</th><th>Rating</th><th>Submitted</th></tr></thead>
        <tbody>
          @forelse($survey->responses as $r)
          <tr>
            <td>{{ $r->stakeholder->name }} ({{ $r->stakeholder->email }})</td>
            <td>{{ $r->rating ?? '-' }}</td>
            <td>{{ $r->submitted_at ? $r->submitted_at->diffForHumans() : 'Pending' }}</td>
          </tr>
          @empty
          <tr><td colspan="3" class="text-center py-4">No responses</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

