<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceAudit;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\ProjectExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use App\Mail\InvoiceSentMail;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(\App\Models\Invoice::class, 'invoice');
    }
    public function index(Request $request)
    {
        $invoices = Invoice::with('project')->accessibleTo(auth()->user())->latest()->paginate(15);
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $projects = Project::all();
        // Clients are Users with role 'CS' within current tenant (renamed from Client)
        $tenantId = \App\Support\Tenancy\TenantManager::getTenantId();
        $clients = \App\Models\User::role('CS')
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->orderBy('name')
            ->get(['id','name','email']);
        $defaultTax = (float) config('finance.tax_percent', 10);
        return view('invoices.create', compact('projects','defaultTax','clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'client_id' => 'required|exists:users,id',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:issue_date',
            'currency' => 'required|string|size:3',
            'tax_percent' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            // Optional initial items
            'item_description' => 'array',
            'item_description.*' => 'nullable|string',
            'item_qty' => 'array',
            'item_qty.*' => 'nullable|numeric|min:0',
            'item_price' => 'array',
            'item_price.*' => 'nullable|numeric|min:0',
        ]);

        $number = self::nextNumber();
        // Resolve client from id (server-trust) and copy snapshot fields
        $client = \App\Models\User::findOrFail($data['client_id']);
        if ($tenantId = \App\Support\Tenancy\TenantManager::getTenantId()) {
            abort_if($client->tenant_id !== $tenantId, 403);
        }
        $invoice = Invoice::create([
            'number' => $number,
            'project_id' => $data['project_id'],
            'client_id' => $client->id,
            'client_name' => $client->name,
            'client_email' => $client->email,
            'issue_date' => $data['issue_date'],
            'due_date' => $data['due_date'] ?? null,
            'status' => 'draft',
            'currency' => $data['currency'],
            'tax_percent' => (float) ($data['tax_percent'] ?? config('finance.tax_percent', 10)),
            'notes' => $data['notes'] ?? null,
            'public_token' => Str::random(40),
        ]);

        $this->buildItemsFromRange($invoice, $data['date_from'] ?? null, $data['date_to'] ?? null);

        // Build initial manual items if provided
        if (!empty($data['item_description'])) {
            $desc = $data['item_description'];
            $qty = $data['item_qty'] ?? [];
            $price = $data['item_price'] ?? [];
            foreach ($desc as $i => $d) {
                $d = trim((string) $d);
                if ($d === '') continue;
                $q = isset($qty[$i]) ? (float)$qty[$i] : 0;
                $p = isset($price[$i]) ? (float)$price[$i] : 0;
                $amount = round($q * $p, 2);
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'type' => 'time',
                    'reference_id' => null,
                    'description' => $d,
                    'quantity' => $q,
                    'unit_price' => $p,
                    'amount' => $amount,
                ]);
            }
        }
        $this->recalculateTotals($invoice);
        $this->audit($invoice, 'created');

        return redirect()->route('invoices.show', $invoice)->with('success','Invoice created');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('project','items');
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');
        $projects = Project::all();
        return view('invoices.edit', compact('invoice','projects'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:users,id',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:issue_date',
            'currency' => 'required|string|size:3',
            'tax_percent' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        $client = \App\Models\User::findOrFail($data['client_id']);
        if ($tenantId = \App\Support\Tenancy\TenantManager::getTenantId()) {
            abort_if($client->tenant_id !== $tenantId, 403);
        }
        $invoice->update([
            'client_id' => $client->id,
            'client_name' => $client->name,
            'client_email' => $client->email,
            'issue_date' => $data['issue_date'],
            'due_date' => $data['due_date'] ?? null,
            'currency' => $data['currency'],
            'tax_percent' => (float) ($data['tax_percent'] ?? $invoice->tax_percent),
            'notes' => $data['notes'] ?? null,
        ]);
        $this->recalculateTotals($invoice);
        $this->audit($invoice, 'updated');
        return redirect()->route('invoices.show', $invoice)->with('success','Invoice updated');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success','Invoice deleted');
    }

    public function approve(Invoice $invoice)
    {
        if (!Auth::user()->can('invoices.approve')) {
            abort(403);
        }
        $this->authorize('update', $invoice);
        if ($invoice->status !== 'draft') {
            return back()->with('error','Only draft invoices can be approved (sent)');
        }
        $invoice->update(['status' => 'sent']);
        $this->audit($invoice, 'approved', ['by' => Auth::id()]);

        // Send email to client (if email available)
        $recipient = $invoice->client_email ?: optional($invoice->project?->creator)->email;
        if ($recipient) {
            $invoiceUrl = route('invoices.public', ['token' => $invoice->public_token]);
            $summaryUrl = URL::temporarySignedRoute('public.project.summary', now()->addDays(7), ['project' => $invoice->project_id]);
            try {
                Mail::to($recipient)->send(new InvoiceSentMail($invoice, $invoiceUrl, $summaryUrl));
            } catch (\Throwable $e) {
                // continue
            }
        }

        return back()->with('success','Invoice approved and sent');
    }

    public function markPaid(Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        if ($invoice->status !== 'sent') {
            return back()->with('error','Only sent invoices can be marked as paid');
        }
        $invoice->update(['status' => 'paid']);
        $this->audit($invoice, 'paid');
        return back()->with('success','Invoice marked as Paid');
    }

    public function pdf(Invoice $invoice)
    {
        // HTML template for now; can be swapped with DomPDF later
        $invoice->load('project','items');
        return view('invoices.pdf', compact('invoice'));
    }

    protected static function nextNumber(): string
    {
        $year = date('Y');
        $prefix = config('finance.invoice_prefix', 'INV');
        $count = Invoice::whereYear('issue_date', $year)->count() + 1;
        return sprintf('%s-%s-%04d', $prefix, $year, $count);
    }

    protected function buildItemsFromRange(Invoice $invoice, ?string $from, ?string $to): void
    {
        if (!$from || !$to) return;
        $projectId = $invoice->project_id;

        $timeEntries = TimeEntry::with(['task'])
            ->where('project_id', $projectId)
            ->where('is_billable', true)
            ->whereBetween('start_time', ["$from 00:00:00", "$to 23:59:59"])
            ->get();
        foreach ($timeEntries as $t) {
            $hours = round(($t->duration ?? 0) / 60, 2);
            $rate = (float) ($t->billable_rate ?? 0);
            $amount = $t->billable_amount ?? ($hours * $rate);
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'type' => 'time',
                'reference_id' => $t->id,
                'description' => 'Time: '.($t->task->title ?? 'Task').' ('.$hours.'h)',
                'quantity' => $hours,
                'unit_price' => $rate,
                'amount' => round((float) $amount, 2),
            ]);
        }

        $expenses = ProjectExpense::where('project_id', $projectId)
            ->where('billable', true)
            ->whereBetween('spent_at', [$from, $to])
            ->get();
        foreach ($expenses as $e) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'type' => 'expense',
                'reference_id' => $e->id,
                'description' => 'Expense: '.$e->description,
                'quantity' => 1,
                'unit_price' => (float)$e->amount,
                'amount' => (float)$e->amount,
            ]);
        }
    }

    protected function recalculateTotals(Invoice $invoice): void
    {
        $subtotal = (float) $invoice->items()->sum('amount');
        $taxPercent = (float) $invoice->tax_percent;
        $taxAmount = round($subtotal * ($taxPercent / 100), 2);
        $total = round($subtotal + $taxAmount, 2);
        $invoice->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $total,
        ]);
    }

    protected function audit(Invoice $invoice, string $action, array $meta = []): void
    {
        InvoiceAudit::create([
            'invoice_id' => $invoice->id,
            'user_id' => Auth::id() ?? 0,
            'action' => $action,
            'meta' => $meta,
        ]);
    }

    // Items management
    public function addItem(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'description' => 'required|string',
            'quantity' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
        ]);
        $amount = round($data['quantity'] * $data['unit_price'], 2);
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'type' => 'time',
            'reference_id' => null,
            'description' => $data['description'],
            'quantity' => $data['quantity'],
            'unit_price' => $data['unit_price'],
            'amount' => $amount,
        ]);
        $this->recalculateTotals($invoice);
        return back()->with('success','Item added');
    }

    public function deleteItem(Invoice $invoice, InvoiceItem $item)
    {
        if ($item->invoice_id !== $invoice->id) abort(404);
        $item->delete();
        $this->recalculateTotals($invoice);
        return back()->with('success','Item removed');
    }

    // Public view
    public function publicShow(string $token)
    {
        $invoice = Invoice::where('public_token', $token)->with('project','items')->firstOrFail();
        return view('invoices.public', compact('invoice'));
    }
}
