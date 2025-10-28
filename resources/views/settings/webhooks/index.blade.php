@extends('layouts.app')
@section('title','Webhooks')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Webhooks</h3>
    <a class="btn btn-primary" href="{{ route('settings.webhooks.create') }}">New Webhook</a>
  </div>
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead class="table-light">
          <tr><th>Event</th><th>Target URL</th><th>Active</th><th>Updated</th><th></th></tr>
        </thead>
        <tbody>
          @forelse($webhooks as $w)
            <tr>
              <td>{{ $w->event }}</td>
              <td class="text-truncate" style="max-width:420px">{{ $w->target_url }}</td>
              <td>{!! $w->active ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
              <td>{{ $w->updated_at->diffForHumans() }}</td>
              <td class="d-flex gap-2">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('settings.webhooks.edit',$w) }}">Edit</a>
                <form method="post" action="{{ route('settings.webhooks.destroy',$w) }}" onsubmit="return confirm('Delete webhook?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center py-4">No webhooks</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $webhooks->links() }}</div>
  </div>
</div>
@endsection

