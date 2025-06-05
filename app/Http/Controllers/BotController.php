<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BotController extends Controller
{

    /**
     * Display the user's bot or all bots for admins.
     */
    public function index()
    {
        $user = Auth::user();

        // For regular users, show only their bot
        $bot = $user->bot;

        // For admins, we'll still show their bot in the index view
        // They can access all bots via the admin.bots route

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
            'activity' => 'required|string',
        ]);

        // Create the bot
        $bot = new Bot([
            'name' => $request->name,
            'token' => $request->token,
            'activity' => $request->activity,
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
        // Check if the bot belongs to the user or if user is admin
        if ($bot->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
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
        // Check if the bot belongs to the user or if user is admin
        if ($bot->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
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
        // Check if the bot belongs to the user or if user is admin
        if ($bot->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
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
        // Check if the bot belongs to the user or if user is admin
        if ($bot->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
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
        // Check if the bot belongs to the user or if user is admin
        if ($bot->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
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
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('bots.index')
                ->with('error', 'Unauthorized access.');
        }

        $bots = Bot::with('user')->get();

        return view('bots.admin', compact('bots'));
    }

    /**
     * Show the bot settings page.
     */
    public function settings(Bot $bot)
    {
        // Check if the bot belongs to the user or if user is admin
        if ($bot->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->route('bots.index')
                ->with('error', 'Unauthorized access.');
        }

        return view('bots.settings', compact('bot'));
    }

    /**
     * Update the bot settings.
     */
    public function updateSettings(Request $request, Bot $bot)
    {
        // Check if the bot belongs to the user or if user is admin
        if ($bot->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->route('bots.index')
                ->with('error', 'Unauthorized access.');
        }

        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'token' => 'nullable|string',
            'activity' => 'required|string',
        ]);

        // Update the bot
        $bot->name = $request->name;
        $bot->activity = $request->activity;

        // Only update token if provided
        if ($request->filled('token')) {
            $bot->token = $request->token;
        }

        $bot->save();

        return redirect()->route('bots.settings', $bot)
            ->with('success', 'Bot settings updated successfully.');
    }

    public function modules(Bot $bot)
    {
        if ($bot->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->route('bots.index')->with('error', 'Unauthorized access.');
        }

        return view('bots.modules', compact('bot'));
    }

    public function updateModules(Request $request, Bot $bot)
    {
        if ($bot->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->route('bots.index')->with('error', 'Unauthorized access.');
        }

        $validated = $request->validate([
            'features' => 'nullable|array',
            'features.*' => 'in:welcome_message,auto_role,moderation,logging',
        ]);

        $bot->features = $validated['features'] ?? [];
        $bot->save();

        return redirect()->back()->with('success', 'Bot settings updated successfully!');
    }

    /**
     * Delete the bot.
     */
    public function destroy(Bot $bot)
    {
        // Check if the bot belongs to the user or if user is admin
        if ($bot->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
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
