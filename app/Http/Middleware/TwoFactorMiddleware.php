<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // If user has 2FA enabled but hasn't verified this session yet
        if ($user && $user->two_factor_enabled && !session('2fa_passed')) {
            return redirect()->route('2fa.verify');
        }

        return $next($request);
    }
}
