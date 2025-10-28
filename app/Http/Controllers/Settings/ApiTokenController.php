<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Http\Request;

class ApiTokenController extends Controller
{
    public function index()
    {
        $tokens = ApiToken::with('user')->latest()->paginate(15);
        $users = User::orderBy('name')->get();
        return view('settings.api-tokens.index', compact('tokens','users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
        ]);

        $plain = bin2hex(random_bytes(32));
        $hash = hash('sha256', $plain);
        ApiToken::create([
            'user_id' => $data['user_id'],
            'name' => $data['name'],
            'token_hash' => $hash,
        ]);

        return redirect()->route('settings.api-tokens.index')
            ->with('success', 'API token created. Save it now; it will not be shown again.')
            ->with('plain_token', $plain);
    }

    public function destroy(ApiToken $api_token)
    {
        $api_token->delete();
        return redirect()->route('settings.api-tokens.index')->with('success','API token deleted');
    }
}

