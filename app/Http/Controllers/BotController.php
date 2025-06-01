<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BotController extends Controller
{

    /**
     * Display the user's bot.
     */
    public function index()
    {
        $bot = Auth::user()->bot;
        return view('bots.index', compact('bot'));
    }

    /**
     * Show the form for creating a new bot.
     */
    public function create()
    {
        // Check if user already has a bot
        if (Auth::user()->bot) {
            return redirect()->route('bots.index')
                ->with('error', 'You can only create one bot.');
        }

        return view('bots.create');
    }

    /**
     * Store a newly created bot in storage.
     */
    public function store(Request $request)
    {
        // Check if user already has a bot
        if (Auth::user()->bot) {
            return redirect()->route('bots.index')
                ->with('error', 'You can only create one bot.');
        }

        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'token' => 'required|string',
        ]);

        // Create the bot
        $bot = new Bot([
            'name' => $request->name,
            'token' => $request->token,
            'status' => 'stopped',
        ]);

        Auth::user()->bot()->save($bot);

        return redirect()->route('bots.index')
            ->with('success', 'Bot created successfully.');
    }

    /**
     * Display the specified bot.
     */
    public function show(Bot $bot)
    {
        // Check if the bot belongs to the user
        if ($bot->user_id !== Auth::id()) {
            return redirect()->route('bots.index')
                ->with('error', 'Unauthorized access.');
        }

        return view('bots.show', compact('bot'));
    }

    /**
     * Start the bot.
     */
    public function start(Bot $bot)
    {
        // Check if the bot belongs to the user
        if ($bot->user_id !== Auth::id() && !Auth::user()->is_admin) {
            return redirect()->route('bots.index')
                ->with('error', 'Unauthorized access.');
        }

        $bot->start();

        return back()->with('success', 'Bot started successfully.');
    }

    /**
     * Stop the bot.
     */
    public function stop(Bot $bot)
    {
        // Check if the bot belongs to the user
        if ($bot->user_id !== Auth::id() && !Auth::user()->is_admin) {
            return redirect()->route('bots.index')
                ->with('error', 'Unauthorized access.');
        }

        $bot->stop();

        return back()->with('success', 'Bot stopped successfully.');
    }

    /**
     * Restart the bot.
     */
    public function restart(Bot $bot)
    {
        // Check if the bot belongs to the user
        if ($bot->user_id !== Auth::id() && !Auth::user()->is_admin) {
            return redirect()->route('bots.index')
                ->with('error', 'Unauthorized access.');
        }

        $bot->restart();

        return back()->with('success', 'Bot restarted successfully.');
    }

    /**
     * Show the bot's logs.
     */
    public function logs(Bot $bot)
    {
        // Check if the bot belongs to the user
        if ($bot->user_id !== Auth::id() && !Auth::user()->is_admin) {
            return redirect()->route('bots.index')
                ->with('error', 'Unauthorized access.');
        }

        $logs = $bot->getLog();

        return view('bots.logs', compact('bot', 'logs'));
    }

    /**
     * Display all bots (admin only).
     */
    public function adminIndex()
    {
        // Check if user is admin
        if (!Auth::user()->is_admin) {
            return redirect()->route('bots.index')
                ->with('error', 'Unauthorized access.');
        }

        $bots = Bot::with('user')->get();

        return view('bots.admin', compact('bots'));
    }

    /**
     * Delete the bot.
     */
    public function destroy(Bot $bot)
    {
        // Check if the bot belongs to the user
        if ($bot->user_id !== Auth::id() && !Auth::user()->is_admin) {
            return redirect()->route('bots.index')
                ->with('error', 'Unauthorized access.');
        }

        if ($bot->delete()) {
            return redirect()->route('bots.index')
                ->with('success', 'Bot deleted successfully.');
        } else {
            return back()->with('error', 'Failed to delete bot.');
        }
    }
}
