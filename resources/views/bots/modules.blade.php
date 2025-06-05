<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bot Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('bots.updateModules', $bot) }}" method="POST">
                        @csrf

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bot Features</label>
                            <div class="space-y-2">
                                @php $features = old('features', $bot->features ?? []) @endphp

                                <x-checkbox id="welcome_message" value="welcome_message" :checked="in_array('welcome_message', $features)">
                                    Welcome Message
                                </x-checkbox>

                                <x-checkbox id="auto_role" value="auto_role" :checked="in_array('auto_role', $features)">
                                    Auto Role
                                </x-checkbox>

                                <x-checkbox id="moderation" value="moderation" :checked="in_array('moderation', $features)">
                                    Moderation Tools
                                </x-checkbox>

                                <x-checkbox id="logging" value="logging" :checked="in_array('logging', $features)">
                                    Logging
                                </x-checkbox>
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
</x-app-layout>
