<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Invoice {{ $invoice->number }}</title>
  <style>
    :root {
      --text:#1a1a1a; --muted:#6b7280; --line:#e5e7eb; --bg:#ffffff; --chip:#f1f5f9; --primary:#0d6efd;
    }
    * { box-sizing: border-box; }
    html, body { margin:0; padding:0; }
    body { font-family: Arial, Helvetica, sans-serif; color: var(--text); background: var(--bg); font-size: 12px; line-height: 1.45; }
    .container { max-width: 900px; margin: 24px auto; padding: 0 16px; }
    .header { display:flex; align-items:flex-start; justify-content:space-between; gap:24px; }
    .brand { display:flex; flex-direction:column; gap:6px; }
    .brand .title { font-size: 26px; font-weight: 700; letter-spacing: .4px; }
    .brand .subtitle { color: var(--muted); font-size: 12px; }
    .meta { text-align:right; }
    .badge { display:inline-block; padding:4px 10px; border-radius: 999px; background: var(--chip); color:#0f172a; font-weight:600; font-size:11px; }
    .status-paid { background:#dcfce7; color:#166534; }
    .status-sent { background:#dbeafe; color:#1e40af; }
    .status-draft { background:#f1f5f9; color:#334155; }

    .section { margin-top: 18px; }
    .grid { display:grid; grid-template-columns: 1fr 1fr; gap:18px; }
    .card { border:1px solid var(--line); border-radius: 8px; overflow:hidden; }
    .card .card-body { padding: 14px 16px; }
    .label { color: var(--muted); font-size: 11px; text-transform: uppercase; letter-spacing: .06em; }

    table { width:100%; border-collapse: collapse; margin-top: 12px; }
    thead th { font-size: 11px; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); background:#f8fafc; }
    th, td { border:1px solid var(--line); padding:10px 8px; vertical-align: top; }
    tbody td.desc { width: 60%; }
    td, th { text-align:left; }
    td.num, th.num { text-align:right; white-space:nowrap; }

    .totals { width: 340px; margin-left:auto; margin-top:10px; }
    .totals td { border:none; padding:6px 8px; }
    .totals tr td:first-child { text-align:right; color:var(--muted); width:60%; }
    .totals .grand td { font-weight:700; border-top:1px solid var(--line); padding-top:8px; }

    .notes { margin-top:18px; padding:12px 14px; background:#f8fafc; border:1px solid var(--line); border-radius:6px; }
    .footer { margin-top:24px; color:var(--muted); font-size:11px; text-align:center; }

    @media print {
      body { background:#fff; }
      .container { margin:0; max-width:none; }
      .notes { break-inside: avoid; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="brand">
        <div class="title">INVOICE</div>
        <div class="subtitle">Invoice No: {{ $invoice->number }}</div>
        <div class="subtitle">Project: {{ $invoice->project->name ?? 'N/A' }}</div>
        @if(optional($invoice->project?->bpomRegistration)->registration_number)
        <div class="subtitle">BPOM: {{ $invoice->project->bpomRegistration->registration_number }} ({{ ucfirst($invoice->project->bpomRegistration->status) }})</div>
        @endif
      </div>
      <div class="meta">
        @php
          $statusClass = $invoice->status==='paid' ? 'status-paid' : ($invoice->status==='sent' ? 'status-sent' : 'status-draft');
          $money = fn($v) => ('Rp '.number_format((float)$v,0,',','.'));
        @endphp
        <div class="badge {{ $statusClass }}">{{ strtoupper($invoice->status) }}</div>
        <div class="section">
          <div><span class="label">Issue Date</span><br>{{ $invoice->issue_date->format('d M Y') }}</div>
          <div style="margin-top:6px;"><span class="label">Due Date</span><br>{{ optional($invoice->due_date)->format('d M Y') ?? '-' }}</div>
          <div style="margin-top:6px;"><span class="label">Total</span><br><strong style="font-size:16px;">{{ $money($invoice->total_amount) }}</strong></div>
        </div>
      </div>
    </div>

    <div class="section grid">
      <div class="card">
        <div class="card-body">
          <div class="label">Bill To</div>
          <div style="margin-top:6px; font-weight:600;">{{ $invoice->client_name ?? 'CS' }}</div>
          <div style="color:var(--muted);">{{ $invoice->client_email ?? '' }}</div>
        </div>
      </div>
      <div class="card">
        <div class="card-body">
          <div class="label">From</div>
          <div style="margin-top:6px; font-weight:600;">{{ config('app.name') }}</div>
          <div style="color:var(--muted);">{{ config('app.url') }}</div>
        </div>
      </div>
    </div>

    <div class="section">
      <table>
        <thead>
          <tr>
            <th>Description</th>
            <th class="num">Qty</th>
            <th class="num">Unit Price</th>
            <th class="num">Amount</th>
          </tr>
        </thead>
        <tbody>
          @foreach($invoice->items as $it)
            <tr>
              <td class="desc">{{ $it->description }}</td>
              <td class="num">{{ number_format((float)$it->quantity,2) }}</td>
              <td class="num">{{ $money($it->unit_price) }}</td>
              <td class="num">{{ $money($it->amount) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <table class="totals">
        <tr><td>Subtotal</td><td class="num">{{ $money($invoice->subtotal) }}</td></tr>
        <tr><td>Tax ({{ number_format((float)$invoice->tax_percent,2) }}%)</td><td class="num">{{ $money($invoice->tax_amount) }}</td></tr>
        <tr class="grand"><td>Total</td><td class="num">{{ $money($invoice->total_amount) }}</td></tr>
      </table>
    </div>

    @if($invoice->notes)
      <div class="notes">
        <div class="label">Notes</div>
        <div style="margin-top:6px; white-space:pre-wrap;">{{ $invoice->notes }}</div>
      </div>
    @endif

    <div class="footer">
      Thank you for your business.
    </div>
  </div>
</body>
</html>
