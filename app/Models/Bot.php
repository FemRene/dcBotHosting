<?php

namespace App\Models;

use App\Services\DockerService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Bot extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'token',
        'activity',
        'status',
        'log_path',
        'container_id',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
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
     *
     * @return bool
     */
    public function start()
    {
        $dockerService = new DockerService();

        try {
            // Check if container exists
            if (!$this->container_id || !$dockerService->containerExists($this->container_id)) {
                try {
                    $containerId = $dockerService->createContainer(
                        (int) $this->id,
                        $this->token,
                        $this->name,
                        $this->activity,
                        implode(";",$this->features)
                    );

                    $this->container_id = $containerId;
                    $this->save();

                    $this->logAction('Bot container created with ID: ' . $this->container_id);
                } catch (\Spatie\Docker\Exceptions\CouldNotStartDockerContainer $e) {
                    // Handle Docker CLI not available error specifically
                    if (strpos($e->getMessage(), 'Docker CLI is not available') !== false) {
                        $this->status = 'error';
                        $this->save();
                        $this->logAction('Error starting bot: ' . $dockerService->getErrorMessage());
                        return false;
                    }
                    throw $e;
                }
            } else {
                // Start existing container
                if (!$dockerService->startContainer($this->container_id)) {
                    $this->status = 'error';
                    $this->save();
                    $this->logAction('Error starting bot: Failed to start container. ' . $dockerService->getErrorMessage());
                    return false;
                }
            }

            $this->status = 'running';
            $this->save();

            $this->logAction('Bot started');
            return true;

        } catch (\Exception $e) {
            Log::error('Failed to start bot: ' . $e->getMessage());
            $this->status = 'error';
            $this->save();
            $this->logAction('Error starting bot: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Stop the bot.
     *
     * @return bool
     */
    public function stop()
    {
        $dockerService = new DockerService();

        try {
            if ($this->container_id) {
                // Check if container exists
                if ($dockerService->containerExists($this->container_id)) {
                    // Try to stop the container
                    if (!$dockerService->stopContainer($this->container_id)) {
                        $this->logAction('Error stopping bot: Failed to stop container. ' . $dockerService->getErrorMessage());
                        // Still mark as stopped since we're attempting to stop it
                        $this->status = 'stopped';
                        $this->save();
                        return false;
                    }
                } else {
                    // Container doesn't exist, log it but consider it stopped
                    $this->logAction('Container does not exist or Docker is not available. ' . $dockerService->getErrorMessage() . ' Marking bot as stopped.');
                }
            }

            $this->status = 'stopped';
            $this->save();

            $this->logAction('Bot stopped');
            return true;

        } catch (\Exception $e) {
            Log::error('Failed to stop bot: ' . $e->getMessage());
            $this->logAction('Error stopping bot: ' . $e->getMessage());
            // Still mark as stopped since we're attempting to stop it
            $this->status = 'stopped';
            $this->save();
            return false;
        }
    }

    /**
     * Restart the bot.
     *
     * @return bool
     */
    public function restart()
    {
        $dockerService = new DockerService();

        try {
            if ($this->container_id) {
                // Check if container exists
                if ($dockerService->containerExists($this->container_id)) {
                    // Try to restart the container
                    if (!$dockerService->restartContainer($this->container_id)) {
                        $this->status = 'error';
                        $this->save();
                        $this->logAction('Error restarting bot: Failed to restart container. ' . $dockerService->getErrorMessage());
                        return false;
                    }

                    $this->status = 'running';
                    $this->save();

                    $this->logAction('Bot restarted');
                    return true;
                } else {
                    // Container doesn't exist, log it and try to start fresh
                    $this->logAction('Container does not exist or Docker is not available. ' . $dockerService->getErrorMessage() . ' Attempting to start fresh.');
                }
            }

            // If no container exists or it couldn't be found, start fresh
            return $this->start();

        } catch (\Exception $e) {
            Log::error('Failed to restart bot: ' . $e->getMessage());
            $this->status = 'error';
            $this->save();
            $this->logAction('Error restarting bot: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete the bot's container and the bot itself.
     *
     * @return bool|null
     */
    public function delete()
    {
        $dockerService = new DockerService();

        try {
            if ($this->container_id) {
                // Check if container exists
                if ($dockerService->containerExists($this->container_id)) {
                    // Try to delete the container
                    if (!$dockerService->deleteContainer($this->container_id)) {
                        $this->logAction('Warning: Failed to delete container. ' . $dockerService->getErrorMessage() . ' Proceeding with database deletion.');
                    } else {
                        $this->logAction('Bot container deleted');
                    }
                } else {
                    // Container doesn't exist or Docker is not available, log it but continue with deletion
                    $this->logAction('Container does not exist or Docker is not available. ' . $dockerService->getErrorMessage() . ' Proceeding with database deletion.');
                }
            }

            // Delete the bot from the database regardless of Docker status
            return parent::delete();

        } catch (\Exception $e) {
            Log::error('Failed to delete bot: ' . $e->getMessage());
            $this->logAction('Error deleting bot: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if container exists by container ID or name.
     *
     * @param string $containerId
     * @return bool
     */
    public function containerExists(string $containerId): bool
    {
        $dockerService = new DockerService();

        try {
            return $dockerService->containerExists($containerId);
        } catch (\Exception $e) {
            Log::error('Error checking container existence: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Log an action to the bot's log file.
     */
    private function logAction(string $message): void
    {
        $logDir = storage_path('logs/bots');

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        if (!$this->log_path) {
            $this->log_path = $logDir . '/' . $this->id . '.log';
            $this->save();
        }

        $timestamp = now()->format('Y-m-d H:i:s');
        file_put_contents($this->log_path, "[$timestamp] $message\n", FILE_APPEND);
    }

    /**
     * Get the bot's log content.
     *
     * @return string
     */
    public function getLog(): string
    {
        $logs = '';

        // Append file logs if available
        if ($this->log_path && file_exists($this->log_path)) {
            $logs .= file_get_contents($this->log_path);
        }

        // Append Docker container logs if container exists
        if ($this->container_id) {
            try {
                $dockerService = new DockerService();

                if ($dockerService->containerExists($this->container_id)) {
                    $containerLogs = $dockerService->getContainerLogs($this->container_id);
                    $logs .= "\n\n-- Container Logs --\n" . $containerLogs;
                }
            } catch (\Exception $e) {
                $logs .= "\n\n-- Failed to get container logs: " . $e->getMessage() . " --\n";
            }
        }

        return $logs;
    }
}
