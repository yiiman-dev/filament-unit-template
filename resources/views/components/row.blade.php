<div {{ $attributes->merge(['class' => 'flex flex-row gap-4']) }}>
    @foreach ($children as $child)
        {!! $child !!}
    @endforeach
</div>
