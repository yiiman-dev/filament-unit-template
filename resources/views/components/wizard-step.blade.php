<div class="flex rounded-2xl w-full gap-5 border border-b-green-400 bg-white pr-8  shadow-md">
    @foreach ($steps as $index => $step)
        <div class="flex-1 flex flex-row gap-3 items-center {{ $loop->first ? '' : 'relative' }}">
            @if (!$loop->first)
            @endif
            <div class="flex items-center text-sm justify-center w-10 h-10 mt-2 mb-2 rounded-full border-2 {{ $currentStep == $index + 1 ? 'border-fuchsia-600 border-2 text-fuchsia-600' : 'border-gray-300 text-gray-400' }} bg-white z-10 text-2xl font-bold">
                @if ($stepIcon)
                    <x-dynamic-component :component="$stepIcon" class="w-6 h-6" />
                @else
                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                @endif
            </div>
            <div class="text-center text-sm {{ $currentStep == $index + 1 ? 'text-fuchsia-600 font-bold' : 'text-gray-500' }}">
                {{ $step['label'] ?? $step }}
            </div>
        </div>
        @if(isset($steps[$index+1]))
            <div class="h-100% -mr-10">
                <svg width="20" height="100" viewBox="0 0 20 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18.8335 0L1.3335 49.5L18.8335 100" stroke="#E5E5E5"/>
                </svg>
            </div>
        @endif

    @endforeach
</div>
