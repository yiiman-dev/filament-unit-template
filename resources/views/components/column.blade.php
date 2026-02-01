<div {{ $attributes->merge(['class' => 'flex flex-col gap-4']) }}>
    @foreach ($children as $child)
        {!! $child !!}
    @endforeach
</div>
