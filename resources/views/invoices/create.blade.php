@extends('layouts.app')
@section('title','Create Invoice')
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">Create Invoice</h3>
  <form method="post" action="{{ route('invoices.store') }}" class="card p-3 border-0 shadow-sm">
    @csrf
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Project</label>
        <select name="project_id" class="form-select" required>
          @foreach($projects as $p)
            <option value="{{ $p->id }}">{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Client</label>
        <select name="client_id" id="client_select" class="form-select" required>
          <option value="">Select client...</option>
          @foreach($clients as $c)
            <option value="{{ $c->id }}" data-name="{{ $c->name }}" data-email="{{ $c->email }}">{{ $c->name }} ({{ $c->email }})</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Client Name</label>
        <input type="text" id="client_name_input" name="client_name" class="form-control" placeholder="" readonly />
      </div>
      <div class="col-md-4">
        <label class="form-label">Client Email</label>
        <input type="email" id="client_email_input" name="client_email" class="form-control" readonly />
      </div>
      <div class="col-md-2">
        <label class="form-label">Issue Date</label>
        <input type="date" name="issue_date" value="{{ now()->toDateString() }}" class="form-control" required />
      </div>
      <div class="col-md-2">
        <label class="form-label">Due Date</label>
        <input type="date" name="due_date" class="form-control" />
      </div>
      <div class="col-md-2">
        <label class="form-label">Currency</label>
        <input type="text" name="currency" class="form-control" value="IDR" maxlength="3" required readonly />
      </div>
      <div class="col-md-2">
        <label class="form-label">Tax %</label>
        <input type="number" step="0.01" name="tax_percent" class="form-control" value="{{ $defaultTax }}" />
      </div>
      <div class="col-md-8">
        <label class="form-label">Notes</label>
        <input type="text" name="notes" class="form-control" />
      </div>
      <div class="col-12">
        <label class="form-label">Build From Range (billable time + expenses)</label>
        <div class="row g-2">
          <div class="col-md-3"><input type="date" name="date_from" class="form-control" /></div>
          <div class="col-md-3"><input type="date" name="date_to" class="form-control" /></div>
          <div class="col-md-6 text-muted small d-flex align-items-center">Optional: leave blank to create empty invoice and add items later.</div>
        </div>
      </div>
      <div class="col-12">
        <label class="form-label">Initial Items (optional)</label>
        <div class="row g-2">
          <div class="col-md-6"><input class="form-control" name="item_description[]" placeholder="Description"></div>
          <div class="col-md-2"><input class="form-control" type="number" step="0.01" name="item_qty[]" placeholder="Qty"></div>
          <div class="col-md-2"><input class="form-control" type="number" step="0.01" name="item_price[]" placeholder="Unit Price"></div>
          <div class="col-md-2 text-muted small d-flex align-items-center">You can add more items after create.</div>
        </div>
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-primary">Create</button>
      <a class="btn btn-outline-secondary" href="{{ route('invoices.index') }}">Cancel</a>
    </div>
  </form>
  <script>
    // Autofill client name & email based on selected client_id
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
        fill();
      }
    })();
  </script>
</div>
@endsection
