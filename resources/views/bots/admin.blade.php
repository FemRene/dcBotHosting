<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin - All Discord Bots') }}
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

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">All Discord Bots</h3>
                        <p class="text-gray-600">Manage all user bots from this admin panel.</p>
                    </div>

                    @if ($bots->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            ID
                                        </th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            User
                                        </th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Created
                                        </th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bots as $bot)
                                        <tr>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ $bot->id }}
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ $bot->name }}
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ $bot->user->name }} ({{ $bot->user->email }})
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                <span class="px-2 py-1 rounded text-white text-xs
                                                    @if ($bot->status == 'running') bg-green-500
                                                    @elseif ($bot->status == 'stopped') bg-red-500
                                                    @else bg-yellow-500
                                                    @endif">
                                                    {{ ucfirst($bot->status) }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                {{ $bot->created_at->format('M d, Y H:i') }}
                                            </td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('bots.logs', $bot) }}" class="bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold py-1 px-2 rounded">
                                                        Logs
                                                    </a>

                                                    @if ($bot->status == 'running')
                                                        <form action="{{ route('bots.stop', $bot) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white text-xs font-bold py-1 px-2 rounded">
                                                                Stop
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('bots.restart', $bot) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white text-xs font-bold py-1 px-2 rounded">
                                                                Restart
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('bots.start', $bot) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white text-xs font-bold py-1 px-2 rounded">
                                                                Start
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-gray-100 p-4 rounded">
                            <p>No bots have been created yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
