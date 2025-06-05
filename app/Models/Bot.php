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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function start()
    {
        $dockerService = new DockerService();

        try {
            if (!$this->container_id || !$dockerService->containerExists($this->container_id)) {
                $containerId = $dockerService->createContainer(
                    (int) $this->id,
                    $this->token,
                    $this->name,
                    $this->activity,
                    implode(";", $this->features),
                );

                $this->container_id = $containerId;
                $this->save();

                $this->logAction('Bot container created with ID: ' . $this->container_id);
            } else {
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

        } catch (\Spatie\Docker\Exceptions\CouldNotStartDockerContainer $e) {
            if (strpos($e->getMessage(), 'Docker CLI is not available') !== false) {
                $this->status = 'error';
                $this->save();
                $this->logAction('Error starting bot: ' . $dockerService->getErrorMessage());
                return false;
            }

            Log::error('Failed to start bot: ' . $e->getMessage());
            $this->status = 'error';
            $this->save();
            $this->logAction('Error starting bot: ' . $e->getMessage());
            return false;
        }
    }

    public function stop()
    {
        $dockerService = new DockerService();

        try {
            if ($this->container_id) {
                if ($dockerService->containerExists($this->container_id)) {
                    if (!$dockerService->stopContainer($this->container_id)) {
                        $this->logAction('Error stopping bot: Failed to stop container. ' . $dockerService->getErrorMessage());
                        $this->status = 'stopped';
                        $this->save();
                        return false;
                    }
                } else {
                    $this->logAction('Container does not exist or Docker is not available. ' . $dockerService->getErrorMessage());
                }
            }

            $this->status = 'stopped';
            $this->save();

            $this->logAction('Bot stopped');
            return true;

        } catch (\Exception $e) {
            Log::error('Failed to stop bot: ' . $e->getMessage());
            $this->logAction('Error stopping bot: ' . $e->getMessage());
            $this->status = 'stopped';
            $this->save();
            return false;
        }
    }

    public function restart()
    {
        $dockerService = new DockerService();

        try {
            if ($this->container_id && $dockerService->containerExists($this->container_id)) {
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
            }

            $this->logAction('Container does not exist or Docker is not available. ' . $dockerService->getErrorMessage() . ' Attempting to start fresh.');
            return $this->start();

        } catch (\Exception $e) {
            Log::error('Failed to restart bot: ' . $e->getMessage());
            $this->status = 'error';
            $this->save();
            $this->logAction('Error restarting bot: ' . $e->getMessage());
            return false;
        }
    }

    public function delete()
    {
        $dockerService = new DockerService();

        try {
            if ($this->container_id && $dockerService->containerExists($this->container_id)) {
                if (!$dockerService->deleteContainer($this->container_id)) {
                    $this->logAction('Warning: Failed to delete container. ' . $dockerService->getErrorMessage());
                } else {
                    $this->logAction('Bot container deleted');
                }
            } else {
                $this->logAction('Container does not exist or Docker is not available. ' . $dockerService->getErrorMessage());
            }

            return parent::delete();

        } catch (\Exception $e) {
            Log::error('Failed to delete bot: ' . $e->getMessage());
            $this->logAction('Error deleting bot: ' . $e->getMessage());
            return false;
        }
    }

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

    public function getLog(): string
    {
        $logs = '';

        if ($this->log_path && file_exists($this->log_path)) {
            $logs .= file_get_contents($this->log_path);
        }

        if ($this->container_id) {
            try {
                $dockerService = new DockerService();

                if ($dockerService->containerExists($this->container_id)) {
                    $logs .= "\n\n-- Container Logs --\n" . $dockerService->getContainerLogs($this->container_id);
                }
            } catch (\Exception $e) {
                $logs .= "\n\n-- Failed to get container logs: " . $e->getMessage() . " --\n";
            }
        }

        return $logs;
    }
}
