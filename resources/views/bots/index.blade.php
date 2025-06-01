<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Discord Bot') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if ($bot)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">{{ $bot->name }}</h3>
                            <p class="mb-2"><strong>Status:</strong>
                                <span class="px-2 py-1 rounded text-white
                                    @if ($bot->status == 'running') bg-green-500
                                    @elseif ($bot->status == 'stopped') bg-red-500
                                    @else bg-yellow-500
                                    @endif">
                                    {{ ucfirst($bot->status) }}
                                </span>
                            </p>
                            <p class="mb-4"><strong>Created:</strong> {{ $bot->created_at->format('M d, Y H:i') }}</p>

                            <div class="flex space-x-2 mb-6">
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
                                <a href="{{ route('bots.logs', $bot) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    View Logs
                                </a>
                            </div>

                            <div class="border-t pt-4">
                                <h4 class="font-semibold mb-2">Bot Token</h4>
                                <div class="bg-gray-100 p-2 rounded">
                                    <code>{{ substr($bot->token, 0, 10) }}...{{ substr($bot->token, -10) }}</code>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">For security reasons, only partial token is displayed.</p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <h3 class="text-lg font-semibold mb-4">You don't have a Discord bot yet</h3>
                            <a href="{{ route('bots.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create a Bot
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
