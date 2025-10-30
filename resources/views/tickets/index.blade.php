@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Tickets</h1>
  <a href="{{ route('tickets.create') }}" class="btn btn-primary mb-3">Buat Ticket</a>
  <table class="table table-striped">
    <thead><tr><th>ID</th><th>No Project</th><th>Judul</th><th>Project</th><th>Status</th><th>Prioritas</th><th></th></tr></thead>
    <tbody>
      @foreach($tickets as $t)
      <tr>
        <td>{{ $t->id }}</td>
        <td>{{ $t->project_sequence ?? '-' }}</td>
        <td>{{ $t->title }}</td>
        <td>{{ optional($t->project)->name }}</td>
        <td>{{ $t->status }}</td>
        <td>{{ $t->priority }}</td>
        <td><a href="{{ route('tickets.show',$t) }}" class="btn btn-sm btn-secondary">Detail</a></td>
      </tr>
      @endforeach
    </tbody>
  </table>
  {{ $tickets->links() }}
</div>
@endsection
