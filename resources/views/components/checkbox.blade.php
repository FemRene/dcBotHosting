@props(['id', 'value', 'checked' => false])

<div class="flex items-center">
    <input type="checkbox" name="features[]" id="{{ $id }}" value="{{ $value }}"
           @if($checked) checked @endif
           class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
    <label for="{{ $id }}" class="ml-2 block text-sm text-gray-700">
        {{ $slot }}
    </label>
</div>
