<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardConroller extends Controller
{
    public function showDashboard()
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        // For admin users, show all users and all bots
        if ($isAdmin) {
            $users = User::all();
            $bots = Bot::with('user')->get();
        } else {
            // For regular users, only show their own bots
            $users = collect([$user]); // Just the current user
            $bots = Bot::where('user_id', $user->id)->with('user')->get();
        }

        return view('dashboard', compact('users', 'bots', 'isAdmin'));
    }
}
