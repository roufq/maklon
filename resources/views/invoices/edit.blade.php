@extends('layouts.app')
@section('title','Edit Invoice ' . $invoice->number)
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">Edit Invoice {{ $invoice->number }}</h3>
  <form method="post" action="{{ route('invoices.update', $invoice) }}" class="card p-3 border-0 shadow-sm">
    @csrf
    @method('PUT')
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Project</label>
        <select name="project_id" class="form-select" disabled>
          @foreach($projects as $p)
            <option value="{{ $p->id }}" @selected($p->id==$invoice->project_id)>{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Client</label>
        <select name="client_id" id="client_select" class="form-select" required>
          @foreach($clients as $c)
            <option value="{{ $c->id }}" data-name="{{ $c->name }}" data-email="{{ $c->email }}" @selected($invoice->client_id==$c->id)>{{ $c->name }} ({{ $c->email }})</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Client Name</label>
        <input type="text" id="client_name_input" class="form-control" value="{{ $invoice->client_name }}" readonly />
      </div>
      <div class="col-md-4">
        <label class="form-label">Client Email</label>
        <input type="email" id="client_email_input" class="form-control" value="{{ $invoice->client_email }}" readonly />
      </div>
      <div class="col-md-2">
        <label class="form-label">Issue Date</label>
        <input type="date" name="issue_date" value="{{ $invoice->issue_date->toDateString() }}" class="form-control" required />
      </div>
      <div class="col-md-2">
        <label class="form-label">Due Date</label>
        <input type="date" name="due_date" value="{{ optional($invoice->due_date)->toDateString() }}" class="form-control" />
      </div>
      <div class="col-md-2">
        <label class="form-label">Currency</label>
        <input type="text" name="currency" class="form-control" value="IDR" maxlength="3" required readonly />
      </div>
      <div class="col-md-2">
        <label class="form-label">Tax %</label>
        <input type="number" step="0.01" name="tax_percent" class="form-control" value="{{ $invoice->tax_percent }}" />
      </div>
      <div class="col-md-8">
        <label class="form-label">Notes</label>
        <input type="text" name="notes" class="form-control" value="{{ $invoice->notes }}" />
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-primary">Save</button>
      <a class="btn btn-outline-secondary" href="{{ route('invoices.show', $invoice) }}">Cancel</a>
    </div>
  </form>
  <script>
    (function(){
      const select = document.getElementById('client_select');
      const nameEl = document.getElementById('client_name_input');
      const emailEl = document.getElementById('client_email_input');
      if (select && nameEl && emailEl) {
        const fill = () => {
          const opt = select.options[select.selectedIndex];
          if (!opt || !opt.dataset) return;
          nameEl.value = opt.dataset.name || '';
          emailEl.value = opt.dataset.email || '';
        };
        select.addEventListener('change', fill);
      }
    })();
  </script>
</div>
@endsection

