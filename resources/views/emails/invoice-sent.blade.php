<h2>Invoice {{ $invoice->number }}</h2>
<p>Dear {{ $invoice->client_name ?? 'Client' }},</p>
<p>We have issued an invoice for project {{ $invoice->project->name ?? '' }}.</p>
<p>
  <a href="{{ $invoiceUrl }}">View Invoice</a>
  | <a href="{{ $summaryUrl }}">View Project Progress</a>
  <br>
  Total: {{ $invoice->currency }} {{ number_format($invoice->total_amount,2) }}
</p>
<p>Thank you.</p>

