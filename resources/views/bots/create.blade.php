<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Discord Bot') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('bots.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Bot Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="token" class="block text-gray-700 text-sm font-bold mb-2">Bot Token</label>
                            <input type="text" name="token" id="token" value="{{ old('token') }}" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('token') border-red-500 @enderror">
                            @error('token')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-600 text-xs mt-1">You can get your bot token from the <a href="https://discord.com/developers/applications" target="_blank" class="text-blue-500 hover:underline">Discord Developer Portal</a>.</p>
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Create Bot
                            </button>
                            <a href="{{ route('bots.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                                Cancel
                            </a>
                        </div>
                    </form>

                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">How to get a Discord Bot Token</h3>
                        <ol class="list-decimal list-inside space-y-2">
                            <li>Go to the <a href="https://discord.com/developers/applications" target="_blank" class="text-blue-500 hover:underline">Discord Developer Portal</a> and log in with your Discord account.</li>
                            <li>Click on "New Application" and give it a name.</li>
                            <li>Navigate to the "Bot" tab in the left sidebar.</li>
                            <li>Click "Add Bot" and confirm by clicking "Yes, do it!"</li>
                            <li>Under the "TOKEN" section, click "Copy" to copy your bot token.</li>
                            <li>Paste the token in the form above.</li>
                        </ol>
                        <p class="mt-4 text-red-600 font-semibold">Important: Never share your bot token with anyone. It provides full access to your bot.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
