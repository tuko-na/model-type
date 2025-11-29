@props(['align' => 'right', 'width' => '48'])

@php
$alignmentClasses = match ($align) {
    'left' => 'left-0',
    'top' => 'bottom-full',
    'right' => 'right-0',
    default => 'right-0',
};

$widthClass = match ($width) {
    '48' => 'w-48',
    '64' => 'w-64',
    '96' => 'w-96',
    'auto' => 'w-auto',
    default => 'w-'.$width,
};
@endphp

<div x-data="{ open: false }" @click.away="open = false" class="relative">
    <div @click="open = !open">
        {{ $trigger }}
    </div>

    <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition-leave-start="transform opacity-100 scale-100"
            x-transition-leave-end="transform opacity-0 scale-95"
            class="absolute z-50 mt-2 {{ $widthClass }} rounded-md shadow-lg {{ $alignmentClasses }}"
            style="display: none;">
        <div class="p-4 bg-white rounded-md ring-1 ring-black ring-opacity-5">
            {{ $content }}
        </div>
    </div>
</div>
