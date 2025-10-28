<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::orderBy('name')->paginate(15);
        $currentId = session('impersonate_tenant_id') ?? (auth()->user()->tenant_id ?? null);
        return view('admin.tenants.index', compact('tenants','currentId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255|unique:tenants,domain',
        ]);
        Tenant::create($data);
        return back()->with('success','Tenant created');
    }

    public function switch(Request $request)
    {
        $request->validate(['tenant_id' => 'nullable|exists:tenants,id']);
        if ($request->tenant_id) {
            session(['impersonate_tenant_id' => (int) $request->tenant_id]);
        } else {
            session()->forget('impersonate_tenant_id');
        }
        return back()->with('success','Tenant switched');
    }
}

