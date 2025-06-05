<?php

namespace App\Services;

use Spatie\Docker\DockerContainer;
use Spatie\Docker\Exceptions\CouldNotStartDockerContainer;
use Illuminate\Support\Facades\Log;

class DockerService
{
    protected string $image;
    protected bool $dockerAvailable;
    protected string $dockerErrorMessage;

    public function __construct()
    {
        $this->image = config('bots.image', 'femrene/femrene-wi-bot:latest');
        $this->dockerAvailable = $this->isDockerAvailable();
        $this->dockerErrorMessage = $this->getDockerErrorMessage();
    }

    protected function isDockerAvailable(): bool
    {
        $output = null;
        $returnVar = null;

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            exec('where docker', $output, $returnVar);
        } else {
            exec('which docker', $output, $returnVar);
        }

        if ($returnVar === 0) {
            exec('docker --version', $output, $returnVar);
        }

        return $returnVar === 0;
    }

    protected function getDockerErrorMessage(): string
    {
        if ($this->dockerAvailable) {
            return '';
        }

        $os = strtoupper(substr(PHP_OS, 0, 3));

        if ($os === 'WIN') {
            return 'Docker CLI is not available. Please install Docker Desktop for Windows and make sure it is running, or add Docker to your PATH.';
        } elseif ($os === 'DAR') {
            return 'Docker CLI is not available. Please install Docker Desktop for Mac and make sure it is running.';
        } else {
            return 'Docker CLI is not available. Please install Docker and make sure it is running.';
        }
    }

    public function getErrorMessage(): string
    {
        return $this->dockerErrorMessage;
    }

    public function createContainer(int $botId, string $botToken, string $botName, string $activity, string $features): string
    {
        if (!$this->dockerAvailable) {
            Log::error($this->dockerErrorMessage);
            throw new CouldNotStartDockerContainer($this->dockerErrorMessage);
        }

        $containerName = "bot-{$botId}";

        try {
            DockerContainer::create($this->image)
                ->name($containerName)
                ->setEnvironmentVariable('BOT_TOKEN', $botToken)
                ->setEnvironmentVariable('BOT_ID', (string)$botId)
                ->setEnvironmentVariable('BOT_NAME', $botName)
                ->setEnvironmentVariable('BOT_ACTIVITY', $activity)
                ->setEnvironmentVariable('BOT_FEATURES', $features)
                ->start();
        } catch (CouldNotStartDockerContainer $e) {
            Log::error('Failed to start Docker container: ' . $e->getMessage());
            throw $e;
        }

        return $containerName;
    }

    public function startContainer(string $containerName): bool
    {
        if (!$this->dockerAvailable) {
            Log::error($this->dockerErrorMessage);
            return false;
        }

        return $this->runCommand("docker start {$containerName}");
    }

    public function stopContainer(string $containerName): bool
    {
        if (!$this->dockerAvailable) {
            Log::error($this->dockerErrorMessage);
            return false;
        }

        return $this->runCommand("docker stop {$containerName}");
    }

    public function restartContainer(string $containerName): bool
    {
        if (!$this->dockerAvailable) {
            Log::error($this->dockerErrorMessage);
            return false;
        }

        return $this->runCommand("docker restart {$containerName}");
    }

    public function deleteContainer(string $containerName): bool
    {
        if (!$this->dockerAvailable) {
            Log::error($this->dockerErrorMessage);
            return false;
        }

        return $this->runCommand("docker rm -f {$containerName}");
    }

    public function containerExists(string $containerName): bool
    {
        if (!$this->dockerAvailable) {
            Log::error($this->dockerErrorMessage);
            return false;
        }

        $output = shell_exec("docker ps -a --filter 'name=^{$containerName}$' --format '{{.Names}}'");
        return trim($output) === $containerName;
    }

    public function getContainerLogs(string $containerName): string
    {
        if (!$this->dockerAvailable) {
            Log::error($this->dockerErrorMessage);
            return $this->dockerErrorMessage;
        }

        return shell_exec("docker logs --tail 100 {$containerName}") ?? '';
    }

    protected function runCommand(string $command): bool
    {
        $output = null;
        $returnVar = null;

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            Log::error("Failed to execute command: {$command}. Return code: {$returnVar}");
            if (!empty($output)) {
                Log::error("Command output: " . implode("\n", $output));
            }
        }

        return $returnVar === 0;
    }
}
