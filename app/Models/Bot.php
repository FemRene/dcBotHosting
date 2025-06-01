<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bot extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'token',
        'status',
        'log_path',
    ];

    /**
     * Get the user that owns the bot.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Start the bot.
     */
    public function start()
    {
        // In a real implementation, this would start the bot process
        // For now, we'll just update the status
        $this->status = 'running';
        $this->save();

        // Log the action
        $this->logAction('Bot started');

        return true;
    }

    /**
     * Stop the bot.
     */
    public function stop()
    {
        // In a real implementation, this would stop the bot process
        // For now, we'll just update the status
        $this->status = 'stopped';
        $this->save();

        // Log the action
        $this->logAction('Bot stopped');

        return true;
    }

    /**
     * Restart the bot.
     */
    public function restart()
    {
        // Stop and then start the bot
        $this->stop();
        $this->start();

        // Log the action
        $this->logAction('Bot restarted');

        return true;
    }

    /**
     * Log an action to the bot's log file.
     */
    private function logAction($message)
    {
        // Create log directory if it doesn't exist
        $logDir = storage_path('logs/bots');
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }

        // Create log file if it doesn't exist
        if (!$this->log_path) {
            $this->log_path = $logDir . '/' . $this->id . '.log';
            $this->save();
        }

        // Append to log file
        $timestamp = now()->format('Y-m-d H:i:s');
        file_put_contents($this->log_path, "[$timestamp] $message\n", FILE_APPEND);
    }

    /**
     * Get the bot's log content.
     */
    public function getLog()
    {
        if (!$this->log_path || !file_exists($this->log_path)) {
            return '';
        }

        return file_get_contents($this->log_path);
    }
}
