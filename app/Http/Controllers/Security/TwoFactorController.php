<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Support\Totp;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    public function settings()
    {
        $user = auth()->user();
        if (!$user->two_factor_enabled && !$user->two_factor_secret) {
            $user->two_factor_secret = Totp::generateSecret();
            $user->save();
        }
        $otpauth = sprintf('otpauth://totp/%s:%s?secret=%s&issuer=%s',
            urlencode(config('app.name')), urlencode($user->email), $user->two_factor_secret, urlencode(config('app.name')));
        return view('security.2fa.settings', compact('user','otpauth'));
    }

    public function enable(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);
        $user = auth()->user();
        if (!$user->two_factor_secret) {
            $user->two_factor_secret = Totp::generateSecret();
        }
        if (!Totp::verify($user->two_factor_secret, $request->code)) {
            return back()->with('error', 'Invalid code.');
        }
        $user->two_factor_enabled = true;
        $user->two_factor_recovery_codes = collect(range(1,8))->map(fn() => Str::random(10))->all();
        $user->save();
        return back()->with('success','Two-factor authentication enabled. Keep your recovery codes safe.');
    }

    public function disable()
    {
        $user = auth()->user();
        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->save();
        session()->forget('two_factor_passed');
        return back()->with('success','Two-factor authentication disabled.');
    }

    public function verifyForm()
    {
        return view('security.2fa.verify');
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required']);
        $user = auth()->user();
        if (!$user || !$user->two_factor_enabled) {
            return redirect()->route('dashboard');
        }
        $code = trim($request->code);
        $ok = false;
        if (ctype_digit($code) && strlen($code) === 6) {
            $ok = Totp::verify($user->two_factor_secret, $code);
        } else {
            // try recovery code
            $codes = collect($user->two_factor_recovery_codes ?? []);
            if ($codes->contains($code)) {
                $ok = true;
                // invalidate used code
                $user->two_factor_recovery_codes = $codes->reject(fn($c) => $c === $code)->values()->all();
                $user->save();
            }
        }
        if (!$ok) return back()->with('error','Invalid code');
        session(['two_factor_passed' => true]);
        return redirect()->intended(route('dashboard'));
    }
}

