<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bot Logs') }}: {{ $bot->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4 flex justify-between items-center">
                        <div>
                            <span class="mr-2">Status:</span>
                            <span class="px-2 py-1 rounded text-white
                                @if ($bot->status == 'running') bg-green-500
                                @elseif ($bot->status == 'stopped') bg-red-500
                                @else bg-yellow-500
                                @endif">
                                {{ ucfirst($bot->status) }}
                            </span>
                        </div>
                        <div>
                            <a href="{{ route('bots.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Back to Bot
                            </a>
                            <button onclick="window.location.reload()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-2">
                                Refresh Logs
                            </button>
                        </div>
                    </div>

                    <div class="bg-gray-900 text-gray-100 p-4 rounded font-mono text-sm overflow-auto" style="max-height: 500px;">
                        @if (empty($logs))
                            <p class="text-gray-400">No logs available yet. Start your bot to generate logs.</p>
                        @else
                            <pre>{{ $logs }}</pre>
                        @endif
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-2">Bot Controls</h3>
                        <div class="flex space-x-2">
                            @if ($bot->status == 'running')
                                <form action="{{ route('bots.stop', $bot) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        Stop Bot
                                    </button>
                                </form>
                                <form action="{{ route('bots.restart', $bot) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                        Restart Bot
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('bots.start', $bot) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        Start Bot
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh logs every 10 seconds if bot is running
        @if ($bot->status == 'running')
        setTimeout(function() {
            window.location.reload();
        }, 10000);
        @endif
    </script>
</x-app-layout>
