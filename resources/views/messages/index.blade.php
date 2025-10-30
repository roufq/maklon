@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Messages</h1>

  <form method="get" class="row g-2 mb-3 align-items-end" id="contextForm">
    <div class="col-md-5">
      <label class="form-label">Pilih Ticket</label>
      <input class="form-control" id="ticketCombo" list="ticketsList" placeholder="Cari/ pilih ticket" value="" />
      <datalist id="ticketsList">
        @isset($tickets)
          @foreach($tickets as $t)
            <option value="T#{{ $t->id }} - {{ $t->title }}@if($t->project) ({{ $t->project->name }}) @endif" data-id="{{ $t->id }}"></option>
          @endforeach
        @endisset
      </datalist>
    </div>
    <div class="col-md-1 text-center">atau</div>
    <div class="col-md-5">
      <label class="form-label">Pilih Project</label>
      <input class="form-control" id="projectCombo" list="projectsList" placeholder="Cari/ pilih project" value="" />
      <datalist id="projectsList">
        @isset($projects)
          @foreach($projects as $p)
            <option value="P#{{ $p->id }} - {{ $p->name }}" data-id="{{ $p->id }}"></option>
          @endforeach
        @endisset
      </datalist>
    </div>
    <input type="hidden" name="ticket_id" id="ticketId" value="{{ request('ticket_id') }}" />
    <input type="hidden" name="project_id" id="projectId" value="{{ request('project_id') }}" />
    <div class="col-md-1">
      <button class="btn btn-outline-primary w-100">Pilih</button>
    </div>
  </form>

  @if($context)
    <div class="mb-3">
      <div class="alert alert-info">Konteks: {{ class_basename($contextType) }} #{{ $contextId }} - {{ $context->name ?? ($context->title ?? '') }}</div>
    </div>
    <ul class="list-group mb-3">
      @forelse($messages as $m)
        <li class="list-group-item">
          <div><strong>{{ optional($m->user)->name }}</strong> <small>{{ $m->created_at }}</small></div>
          @if($m->body)
            <div>{{ $m->body }}</div>
          @endif
          @if($m->attachment_path)
            <div><a target="_blank" href="{{ asset('storage/'.$m->attachment_path) }}">Lampiran</a></div>
          @endif
        </li>
      @empty
        <li class="list-group-item">Belum ada pesan</li>
      @endforelse
    </ul>

    <form method="post" action="{{ route('messages.store') }}" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="ticket_id" value="{{ request('ticket_id') }}" />
      <input type="hidden" name="project_id" value="{{ request('project_id') }}" />
      <div class="mb-3">
        <textarea class="form-control" name="body" rows="3" placeholder="Tulis pesan..."></textarea>
      </div>
      <div class="mb-3">
        <input type="file" name="file" class="form-control" />
      </div>
      <button class="btn btn-primary">Kirim</button>
    </form>
@else
    <div class="alert alert-warning">Pilih Ticket atau Project terlebih dahulu untuk melihat/mengirim pesan.</div>
@endif
</div>
@endsection

@push('scripts')
<script>
  (function(){
    const ticketCombo = document.getElementById('ticketCombo');
    const projectCombo = document.getElementById('projectCombo');
    const ticketList = document.getElementById('ticketsList');
    const projectList = document.getElementById('projectsList');
    const ticketId = document.getElementById('ticketId');
    const projectId = document.getElementById('projectId');

    function syncDatalist(inputEl, listEl, hiddenEl, otherHiddenEl, otherInput){
      const val = inputEl.value;
      const match = Array.from(listEl.options).find(o => o.value === val);
      const id = match ? match.getAttribute('data-id') : '';
      hiddenEl.value = id || '';
      if (id) { otherHiddenEl.value=''; if (otherInput) otherInput.value=''; }
    }
    ticketCombo && ticketCombo.addEventListener('change', ()=> syncDatalist(ticketCombo, ticketList, ticketId, projectId, projectCombo));
    projectCombo && projectCombo.addEventListener('change', ()=> syncDatalist(projectCombo, projectList, projectId, ticketId, ticketCombo));

    // Pre-fill visible input from existing hidden id (on back nav)
    if (ticketId.value) {
      const opt = Array.from(ticketList.options).find(o => o.getAttribute('data-id')===ticketId.value);
      if (opt) ticketCombo.value = opt.value;
    }
    if (projectId.value) {
      const opt = Array.from(projectList.options).find(o => o.getAttribute('data-id')===projectId.value);
      if (opt) projectCombo.value = opt.value;
    }
  })();
  </script>
@endpush
