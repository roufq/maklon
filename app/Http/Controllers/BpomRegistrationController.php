<?php

namespace App\Http\Controllers;

use App\Models\BpomRegistration;
use App\Models\BpomAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Support\Tenancy\TenantManager;
use Illuminate\Support\Facades\Gate;

class BpomRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeView();
        $items = BpomRegistration::query()
            ->when($request->get('status'), fn($q,$s)=>$q->where('status',$s))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();
        return view('bpom.index', compact('items'));
    }

    public function create()
    {
        $this->authorizeCreate();
        return view('bpom.create');
    }

    public function store(Request $request)
    {
        $this->authorizeCreate();
        $data = $request->validate([
            'product_name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255|unique:bpom_registrations,registration_number',
            'approval_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:approval_date',
            'status' => 'required|in:draft,active,expired,revoked,pending',
            'document' => 'nullable|file|mimetypes:application/pdf,image/jpeg,image/png|max:10240',
        ]);
        $path = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $path = $file->store('bpom_docs', 'local'); // private storage
        }
        $item = BpomRegistration::create([
            'product_name' => $data['product_name'],
            'registration_number' => $data['registration_number'],
            'approval_date' => $data['approval_date'] ?? null,
            'expiry_date' => $data['expiry_date'] ?? null,
            'status' => $data['status'],
            'document_path' => $path,
        ]);
        BpomAudit::create([
            'bpom_registration_id' => $item->id,
            'user_id' => auth()->id(),
            'action' => 'created',
            'meta' => ['status' => $item->status],
        ]);
        return redirect()->route('bpom.show', $item)->with('success','BPOM registration created');
    }

    public function show(BpomRegistration $bpom)
    {
        $this->authorizeView();
        if ($bpom->tenant_id && TenantManager::getTenantId() && $bpom->tenant_id !== TenantManager::getTenantId()) {
            abort(404);
        }
        // Provide both keys to satisfy different view/test expectations
        return view('bpom.show', ['item' => $bpom, 'registration' => $bpom]);
    }

    public function edit(BpomRegistration $bpom)
    {
        $this->authorizeEdit();
        return view('bpom.edit', ['item' => $bpom]);
    }

    public function update(Request $request, BpomRegistration $bpom)
    {
        $this->authorizeEdit();
        $data = $request->validate([
            'product_name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255|unique:bpom_registrations,registration_number,'.$bpom->id,
            'approval_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:approval_date',
            'status' => 'required|in:draft,active,expired,revoked,pending',
            'document' => 'nullable|file|mimetypes:application/pdf,image/jpeg,image/png|max:10240',
        ]);
        if ($request->hasFile('document')) {
            if ($bpom->document_path && Storage::disk('local')->exists($bpom->document_path)) {
                Storage::disk('local')->delete($bpom->document_path);
            }
            $bpom->document_path = $request->file('document')->store('bpom_docs', 'local');
        }
        $before = $bpom->getOriginal();
        $bpom->update([
            'product_name' => $data['product_name'],
            'registration_number' => $data['registration_number'],
            'approval_date' => $data['approval_date'] ?? null,
            'expiry_date' => $data['expiry_date'] ?? null,
            'status' => $data['status'],
            'document_path' => $bpom->document_path,
        ]);
        BpomAudit::create([
            'bpom_registration_id' => $bpom->id,
            'user_id' => auth()->id(),
            'action' => 'updated',
            'meta' => ['before' => $before, 'after' => $bpom->getAttributes()],
        ]);
        return redirect()->route('bpom.show', $bpom)->with('success','BPOM registration updated');
    }

    public function export(Request $request)
    {
        $this->authorizeView();
        $query = BpomRegistration::query()
            ->when($request->status, fn($q,$s)=>$q->where('status',$s))
            ->orderByDesc('id');
        $filename = 'bpom-registrations-'.now()->format('Y-m-d').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];
        $callback = function () use ($query) {
            $out = fopen('php://output','w');
            fputcsv($out, ['Product','Registration Number','Approval','Expiry','Status']);
            $query->clone()->chunkById(1000, function($items) use ($out){
                foreach ($items as $i) {
                    fputcsv($out, [
                        $i->product_name,
                        $i->registration_number,
                        optional($i->approval_date)->format('Y-m-d'),
                        optional($i->expiry_date)->format('Y-m-d'),
                        $i->status,
                    ]);
                }
                if (function_exists('ob_flush')) { @ob_flush(); }
                flush();
            }, 'id');
            fclose($out);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function destroy(BpomRegistration $bpom)
    {
        $this->authorizeDelete();
        if ($bpom->document_path && Storage::disk('local')->exists($bpom->document_path)) {
            Storage::disk('local')->delete($bpom->document_path);
        }
        // Log audit BEFORE deletion to satisfy FK constraints
        BpomAudit::create([
            'bpom_registration_id' => $bpom->id,
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'meta' => null,
        ]);
        $bpom->delete();
        return redirect()->route('bpom.index')->with('success','BPOM registration deleted');
    }

    public function download(BpomRegistration $bpom)
    {
        $this->authorizeView();
        abort_unless($bpom->document_path && Storage::disk('local')->exists($bpom->document_path), 404);
        BpomAudit::create([
            'bpom_registration_id' => $bpom->id,
            'user_id' => auth()->id(),
            'action' => 'downloaded',
            'meta' => null,
        ]);
        return response()->download(storage_path('app/'.$bpom->document_path));
    }

    public function activate(BpomRegistration $bpom)
    {
        $this->authorizeEdit();
        $from = $bpom->status;
        $bpom->update(['status' => 'active']);
        BpomAudit::create([
            'bpom_registration_id' => $bpom->id,
            'user_id' => auth()->id(),
            'action' => 'status_changed',
            'meta' => ['from' => $from, 'to' => 'active'],
        ]);
        return back()->with('success','Status set to Active');
    }

    public function revoke(BpomRegistration $bpom)
    {
        $this->authorizeEdit();
        $from = $bpom->status;
        $bpom->update(['status' => 'revoked']);
        BpomAudit::create([
            'bpom_registration_id' => $bpom->id,
            'user_id' => auth()->id(),
            'action' => 'status_changed',
            'meta' => ['from' => $from, 'to' => 'revoked'],
        ]);
        return back()->with('success','Status set to Revoked');
    }

    private function authorizeView(): void
    {
        abort_unless(auth()->user()?->can('bpom.view'), 403);
    }
    private function authorizeCreate(): void
    {
        abort_unless(auth()->user()?->can('bpom.create'), 403);
    }
    private function authorizeEdit(): void
    {
        abort_unless(auth()->user()?->can('bpom.edit'), 403);
    }
    private function authorizeDelete(): void
    {
        abort_unless(auth()->user()?->can('bpom.delete'), 403);
    }
}
