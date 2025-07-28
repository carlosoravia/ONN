@props([
    'size' => '12',
    'color' => 'blue-500',
    'message' => null,
])

<div class="flex flex-col items-center justify-center space-y-2">
    <div class="w-{{ $size }} h-{{ $size }} border-4 border-{{ $color }} border-t-transparent rounded-full animate-spin"></div>

    @if($message)
        <p class="text-sm text-azure-700">{{ $message }}</p>
    @endif
</div>
