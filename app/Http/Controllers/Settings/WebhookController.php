<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Webhook;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function index()
    {
        $webhooks = Webhook::latest()->paginate(15);
        return view('settings.webhooks.index', compact('webhooks'));
    }

    public function create()
    {
        return view('settings.webhooks.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'event' => 'required|string|max:255',
            'target_url' => 'required|url',
            'secret' => 'nullable|string|max:255',
            'active' => 'nullable|boolean',
        ]);
        $data['active'] = (bool) ($data['active'] ?? true);
        Webhook::create($data);
        return redirect()->route('settings.webhooks.index')->with('success','Webhook created');
    }

    public function edit(Webhook $webhook)
    {
        return view('settings.webhooks.edit', compact('webhook'));
    }

    public function update(Request $request, Webhook $webhook)
    {
        $data = $request->validate([
            'event' => 'required|string|max:255',
            'target_url' => 'required|url',
            'secret' => 'nullable|string|max:255',
            'active' => 'nullable|boolean',
        ]);
        $data['active'] = (bool) ($data['active'] ?? false);
        $webhook->update($data);
        return redirect()->route('settings.webhooks.index')->with('success','Webhook updated');
    }

    public function destroy(Webhook $webhook)
    {
        $webhook->delete();
        return redirect()->route('settings.webhooks.index')->with('success','Webhook deleted');
    }
}

