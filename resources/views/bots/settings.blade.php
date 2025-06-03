<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bot Settings') }}
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

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Bot Settings</h3>

                        <form action="{{ route('bots.updateSettings', $bot) }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Bot Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $bot->name) }}"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    required>
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="token" class="block text-sm font-medium text-gray-700 mb-1">Bot Token</label>
                                <div class="flex items-center">
                                    <input type="password" name="token" id="token" placeholder="Enter new token (leave empty to keep current)"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <button type="button" id="toggleToken" class="ml-2 px-3 py-1 bg-gray-200 rounded-md text-sm">Show</button>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">Current token: <code>{{ substr($bot->token, 0, 10) }}...{{ substr($bot->token, -10) }}</code></p>
                                <p class="text-sm text-gray-600 mt-1">Leave empty to keep the current token.</p>
                                @error('token')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center space-x-4">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Save Settings
                                </button>
                                <a href="{{ route('bots.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('toggleToken');
            const tokenInput = document.getElementById('token');

            toggleButton.addEventListener('click', function() {
                if (tokenInput.type === 'password') {
                    tokenInput.type = 'text';
                    toggleButton.textContent = 'Hide';
                } else {
                    tokenInput.type = 'password';
                    toggleButton.textContent = 'Show';
                }
            });
        });
    </script>
</x-app-layout>
