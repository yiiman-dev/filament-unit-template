@props(['label', 'icon', 'color'])

@php
    $colorClasses = [
        'success' => 'bg-green-100 text-green-800 border-green-200',
        'danger' => 'bg-red-100 text-red-800 border-red-200',
        'warning' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'gray' => 'bg-gray-100 text-gray-800 border-gray-200',
        'info' => 'bg-blue-100 text-blue-800 border-blue-200',
    ];
    
    $iconClasses = [
        'success' => 'text-green-500',
        'danger' => 'text-red-500',
        'warning' => 'text-yellow-500',
        'gray' => 'text-gray-500',
        'info' => 'text-blue-500',
    ];
    
    $colorClass = $colorClasses[$color] ?? $colorClasses['gray'];
    $iconClass = $iconClasses[$color] ?? $iconClasses['gray'];
@endphp

<div class="flex items-center justify-center p-4">
    <div class="flex flex-col items-center space-y-2">
        <div class="flex items-center justify-center w-12 h-12 rounded-full {{ $colorClass }} border-2">
            <svg class="w-6 h-6 {{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                @if($icon === 'heroicon-o-check-circle')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                @elseif($icon === 'heroicon-o-x-circle')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                @elseif($icon === 'heroicon-o-exclamation-triangle')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                @elseif($icon === 'heroicon-o-information-circle')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                @elseif($icon === 'heroicon-o-clock')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                @else
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                @endif
            </svg>
        </div>
        <span class="text-sm font-medium text-center">{{ $label }}</span>
    </div>
</div>


