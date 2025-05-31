<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FAQRCode\Google2FA;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    public function showSetupForm()
    {
        $user = Auth::user();

        if ($user->two_factor_enabled) {
            return redirect()->route('home')->with('status', '2FA already enabled.');
        }

        $google2fa = new Google2FA();

        // Generate new secret for user
        $secret = $google2fa->generateSecretKey();

        // Generate QR code url for Google Authenticator app
        $inlineUrl = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $secret
        );

        // Store secret temporarily in session, user must confirm code
        session(['google2fa_secret' => $secret]);

        return view('2fa.setup', [
            'qrCode' => $inlineUrl,
            'secret' => $secret,
        ]);
    }

    public function enableTwoFactor(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|digits:6',
        ]);

        $user = Auth::user();
        $google2fa = new Google2FA();

        $secret = session('google2fa_secret');

        if (!$secret) {
            return redirect()->route('2fa.setup')->withErrors('Secret key missing. Please try again.');
        }

        // Verify OTP code user entered
        $valid = $google2fa->verifyKey($secret, $request->one_time_password);

        if ($valid) {
            // Save secret and enable 2FA on user
            $user->google2fa_secret = $secret;
            $user->two_factor_enabled = true;
            $user->save();

            session()->forget('google2fa_secret');

            return redirect()->route('home')->with('status', 'Two-factor authentication enabled successfully!');
        } else {
            return redirect()->back()->withErrors('Invalid verification code, please try again.');
        }
    }

    public function showVerifyForm()
    {
        return view('2fa.verify');
    }

    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|digits:6',
        ]);

        $user = Auth::user();
        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->one_time_password);

        if ($valid) {
            // Mark 2FA passed in session
            session(['2fa_passed' => true]);

            return redirect()->intended('/home'); // or dashboard
        } else {
            return redirect()->back()->withErrors('Invalid verification code, please try again.');
        }
    }
}
