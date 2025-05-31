<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class DiscordController extends Controller
{
    // Redirect user to Discord OAuth
    public function redirectToDiscord()
    {
        return Socialite::driver('discord')->redirect();
    }

    // Handle callback from Discord
    public function handleDiscordCallback()
    {
        try {
            $discordUser = Socialite::driver('discord')->user();
        } catch (\Exception $e) {
            return redirect('/login')->withErrors('Failed to login with Discord.');
        }

        // Check if user with this discord_id exists
        $user = User::where('discord_id', $discordUser->id)->first();

        if (!$user) {
            // No user with this discord_id, check if email exists
            if ($discordUser->email) {
                $user = User::where('email', $discordUser->email)->first();

                if ($user) {
                    // Attach discord_id to existing user
                    $user->discord_id = $discordUser->id;
                    $user->avatar = $discordUser->avatar ?? $user->avatar;
                    $user->token = $discordUser->token;
                    $user->save();
                } else {
                    // No user with this email - create new
                    $user = User::create([
                        'discord_id' => $discordUser->id,
                        'name' => $discordUser->nickname ?? $discordUser->name ?? 'NoName',
                        'email' => $discordUser->email,
                        'avatar' => $discordUser->avatar ?? null,
                        'token' => $discordUser->token,
                    ]);
                }
            } else {
                // No email provided, just create new user with discord_id
                $user = User::create([
                    'discord_id' => $discordUser->id,
                    'name' => $discordUser->nickname ?? $discordUser->name ?? 'NoName',
                    'email' => null,
                    'avatar' => $discordUser->avatar ?? null,
                    'token' => $discordUser->token,
                ]);
            }
        } else {
            // User with discord_id found - update info
            $user->name = $discordUser->nickname ?? $discordUser->name ?? $user->name;
            $user->email = $discordUser->email ?? $user->email;
            $user->avatar = $discordUser->avatar ?? $user->avatar;
            $user->token = $discordUser->token;
            $user->save();
        }

        Auth::login($user, true);

        // 🔐 TOTP 2FA Handling
        if ($user->two_factor_enabled) {
            session()->forget('2fa_passed');
            return redirect()->route('2fa.verify');
        }

        return redirect()->route('2fa.setup');
    }
}
