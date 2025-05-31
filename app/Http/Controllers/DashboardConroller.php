<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardConroller extends Controller
{
    public function showDashboard()
    {
        $users = User::all();
        $bots = Bot::with('user')->get();
        return view('dashboard', compact('users', 'bots'));
    }
}
