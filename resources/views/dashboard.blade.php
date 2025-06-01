<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-{{ $isAdmin ? '3' : '2' }}">
            @if($isAdmin)
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 overflow-auto">
                    <h2 class="text-lg font-semibold mb-2">Registered Users</h2>
                    <ul class="text-sm max-h-full overflow-auto">
                        @forelse($users as $user)
                            <li class="border-b border-neutral-300 dark:border-neutral-700 py-1">
                                {{ $user->name }} ({{ $user->email }})
                            </li>
                        @empty
                            <li>No users found.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            @endif
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 overflow-auto">
                    <div class="flex justify-between items-center mb-2">
                        <h2 class="text-lg font-semibold">Discord Bots</h2>
                        @if($isAdmin)
                        <a href="{{ route('admin.bots') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            Manage All Bots →
                        </a>
                        @endif
                    </div>
                    <ul class="text-sm max-h-full overflow-auto">
                        @forelse($bots as $bot)
                            <li class="border-b border-neutral-300 dark:border-neutral-700 py-1">
                                {{ $bot->name }} ({{ $bot->user->name }}) - {{ ucfirst($bot->status) }}
                            </li>
                        @empty
                            <li>No bots found.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div>
</x-layouts.app>
