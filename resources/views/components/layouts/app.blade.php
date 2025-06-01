<x-layouts.app.sidebar :title="$title ?? null">
    @if (isset($header))
        <x-slot name="header">
            {{ $header }}
        </x-slot>
    @endif
    <flux:main>
        {{ $slot }}
    </flux:main>
</x-layouts.app.sidebar>
