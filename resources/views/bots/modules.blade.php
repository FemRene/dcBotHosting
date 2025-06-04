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

                        <form action="{{ route('bots.updateModules', $bot) }}" method="POST">
                            @csrf

                            <!-- New Section: Bot Feature Toggles -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Features</label>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="welcome_message" id="welcome_message" value="1" {{ old('welcome_message', $bot->welcome_message) ? 'checked' : '' }}
                                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <label for="welcome_message" class="ml-2 block text-sm text-gray-700">Welcome Message</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="auto_role" id="auto_role" value="1" {{ old('auto_role', $bot->auto_role) ? 'checked' : '' }}
                                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <label for="auto_role" class="ml-2 block text-sm text-gray-700">Auto Role</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="moderation" id="moderation" value="1" {{ old('moderation', $bot->moderation) ? 'checked' : '' }}
                                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <label for="moderation" class="ml-2 block text-sm text-gray-700">Moderation Tools</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="logging" id="logging" value="1" {{ old('logging', $bot->logging) ? 'checked' : '' }}
                                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <label for="logging" class="ml-2 block text-sm text-gray-700">Logging</label>
                                    </div>
                                </div>
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
                tokenInput.type = tokenInput.type === 'password' ? 'text' : 'password';
                toggleButton.textContent = tokenInput.type === 'password' ? 'Show' : 'Hide';
            });
        });
    </script>
</x-app-layout>
