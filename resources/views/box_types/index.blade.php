@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Box Types</h1>
  @can('permission','production.create')
  <a href="{{ route('box-types.create') }}" class="btn btn-primary mb-3">Tambah Box Type</a>
  @endcan
  <table class="table table-striped">
    <thead><tr><th>Nama</th><th>Deskripsi</th><th></th></tr></thead>
    <tbody>
    @foreach($types as $t)
      <tr>
        <td>{{ $t->name }}</td>
        <td>{{ $t->description }}</td>
        <td>
          @can('permission','production.edit')
          <a href="{{ route('box-types.edit',$t) }}" class="btn btn-sm btn-secondary">Edit</a>
          @endcan
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
  {{ $types->links() }}
</div>
@endsection

