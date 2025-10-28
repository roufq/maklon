@extends('layouts.app')
@section('title','Edit Webhook')
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">Edit Webhook</h3>
  <form method="post" action="{{ route('settings.webhooks.update',$webhook) }}" class="card p-3 border-0 shadow-sm">
    @csrf @method('PUT')
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Event</label>
        <input class="form-control" name="event" value="{{ $webhook->event }}" required />
      </div>
      <div class="col-md-6">
        <label class="form-label">Target URL</label>
        <input class="form-control" name="target_url" value="{{ $webhook->target_url }}" required />
      </div>
      <div class="col-md-6">
        <label class="form-label">Secret (optional)</label>
        <input class="form-control" name="secret" value="{{ $webhook->secret }}" />
      </div>
      <div class="col-md-6 d-flex align-items-end gap-2">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="active" value="1" id="activeCheck" {{ $webhook->active ? 'checked' : '' }}>
          <label class="form-check-label" for="activeCheck">Active</label>
        </div>
      </div>
    </div>
    <div class="mt-3 d-flex gap-2">
      <button class="btn btn-primary">Update</button>
      <a class="btn btn-outline-secondary" href="{{ route('settings.webhooks.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection

