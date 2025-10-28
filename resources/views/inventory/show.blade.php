@extends('layouts.app')
@section('title','Inventory ' . $item->name)
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">{{ $item->name }}</h3>
    <div class="d-flex gap-2">
      @if(auth()->user()->can('permission','inventory.edit'))
      <a class="btn btn-outline-primary" href="{{ route('inventory.edit',$item) }}">Edit</a>
      @endif
      @if(auth()->user()->can('permission','inventory.delete'))
      <form method="post" action="{{ route('inventory.destroy',$item) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-outline-danger">Delete</button></form>
      @endif
    </div>
  </div>
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-6">
          <div><strong>Supplier:</strong> {{ $item->supplier->name ?? '-' }}</div>
          <div><strong>Unit:</strong> {{ $item->unit ?? '-' }}</div>
          <div><strong>Unit Cost:</strong> Rp {{ number_format($item->unit_cost ?? 0,0,',','.') }}</div>
        </div>
        <div class="col-md-6">
          <div><strong>Stock:</strong> {{ $item->current_stock }}</div>
          <div><strong>Min Stock:</strong> {{ $item->min_stock }}</div>
        </div>
      </div>
      @if(auth()->user()->can('permission','inventory.edit'))
      <form class="row g-2 align-items-end" method="post" action="{{ route('inventory.movement',$item) }}">
        @csrf
        <div class="col-md-2">
          <label class="form-label">Type</label>
          <select class="form-select" name="type"><option value="in">In</option><option value="out">Out</option><option value="adjust">Adjust</option></select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Qty</label>
          <input type="number" class="form-control" name="qty" placeholder="Qty" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Project (for expense)</label>
          <select class="form-select" name="project_id">
            <option value="">— Optional —</option>
            @isset($projects)
              @foreach($projects as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
              @endforeach
            @endisset
          </select>
        </div>
        <div class="col-md-2">
          <button class="btn btn-outline-primary">Save Movement</button>
        </div>
      </form>
      @endif
      <div class="table-responsive mt-3">
        <table class="table mb-0">
          <thead class="table-light"><tr><th>Date</th><th>Type</th><th>Qty</th><th>By</th><th>Project</th></tr></thead>
          <tbody>@foreach($item->movements as $m)<tr>
            <td>{{ $m->created_at->format('Y-m-d H:i') }}</td>
            <td>{{ strtoupper($m->type) }}</td>
            <td>{{ $m->qty }}</td>
            <td>{{ $m->user->name ?? '-' }}</td>
            <td>
              @if($m->reference_type === App\Models\Project::class && $m->reference_id)
                {{ optional(App\Models\Project::find($m->reference_id))->name }}
              @else
                -
              @endif
            </td>
          </tr>@endforeach</tbody>
      </table>
      </div>
    </div>
  </div>
</div>
@endsection
