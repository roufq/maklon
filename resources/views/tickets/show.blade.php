@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Ticket #{{ $ticket->id }} - {{ $ticket->title }}</h1>
  <p>Project: {{ optional($ticket->project)->name }} | No Project: <strong>{{ $ticket->project_sequence ?? '-' }}</strong> | Status: <strong>{{ $ticket->status }}</strong> | Prioritas: {{ $ticket->priority }}</p>
  @can('permission','projects.edit')
  @if($ticket->status !== 'closed')
  <form method="post" action="{{ route('tickets.close',$ticket) }}" class="mb-3">
    @csrf
    <button class="btn btn-warning">Tutup Ticket</button>
  </form>
  @endif
  @endcan

  <h4>Pesan</h4>
  <div class="mb-2">
    <a class="btn btn-sm btn-outline-info" href="{{ route('messages.index',['ticket_id'=>$ticket->id]) }}">Open Messages</a>
  </div>
  <ul class="list-group mb-3">
    @foreach($ticket->messages as $m)
      <li class="list-group-item">
        <div><strong>{{ optional($m->user)->name }}</strong> <small>{{ $m->created_at }}</small></div>
        @if($m->message)
          <div>{{ $m->message }}</div>
        @endif
        @if($m->attachment_path)
          <div><a href="{{ asset('storage/'.$m->attachment_path) }}" target="_blank">Lampiran</a></div>
        @endif
      </li>
    @endforeach
  </ul>

  <h5>Kirim Pesan</h5>
  <form method="post" action="{{ route('tickets.messages.store',$ticket) }}" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
      <textarea name="message" class="form-control" rows="3" placeholder="Tulis pesan..."></textarea>
    </div>
    <div class="mb-3">
      <input type="file" name="file" class="form-control" />
    </div>
    <button class="btn btn-primary">Kirim</button>
  </form>
</div>
@endsection
