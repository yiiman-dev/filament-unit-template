@php
    use Filament\Support\Enums\IconPosition;
    use Filament\Support\Facades\FilamentView;

    $chartColor = $getChartColor() ?? 'gray';
    $descriptionColor = $getDescriptionColor() ?? 'gray';
    $descriptionIcon = $getDescriptionIcon();
    $url = $getUrl();
    $tag = $url ? 'a' : 'div';
    $dataChecksum = $generateDataChecksum();

    // استخراج تعداد و درصد از description
    $description = $getDescription();
    $count = null;
    $percentage = null;
    if ($description) {
        if (preg_match('/تعداد:\s*(\d+)/', $description, $matches)) {
            $count = $matches[1];
        }
        if (preg_match('/(\d+)%\s*(افزایش|کاهش)/', $description, $matches)) {
            $percentage = $matches[1] . '% ' . $matches[2];
        }
    }

    $isIncrease = $description && str_contains($description, 'افزایش');
    $trendColor = $isIncrease ? 'success' : 'danger';
@endphp

<{!! $tag !!}
@if ($url)
    {{ \Filament\Support\generate_href_html($url, $shouldOpenUrlInNewTab()) }}
@endif
{{
    $getExtraAttributeBag()
        ->class([
            'fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10',
        ])
}}
>
<div class="space-y-4">
    {{-- Header: Count Badge (Left) and Title (Right) --}}
    <div class="flex items-center justify-between">

             <span class="text-sm font-semibold text-gray-900 dark:text-white">
                {{ $getLabel() }}
            </span>
        <span
            class="inline-flex items-center rounded-md bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                    تعداد: {{ $count }}
                </span>


    </div>

    {{-- Main Value with Unit --}}
    <div class="flex items-baseline gap-x-2">
         <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                ارزش (میلیارد ریال)
            </span>
        <div class="text-3xl font-bold tracking-tight text-gray-950 dark:text-white">
            {{ $getValue() }}
        </div>

    </div>

    {{-- Percentage Change with Icon --}}
    @if ($percentage)
        <div class="flex items-center gap-x-1">
            @if ($descriptionIcon)
                <x-filament::icon
                    :icon="$descriptionIcon"
                    @class([
                        'h-4 w-4',
                        match ($trendColor) {
                            'success' => 'text-success-600 dark:text-success-400',
                            'danger' => 'text-danger-600 dark:text-danger-400',
                            default => 'text-gray-400 dark:text-gray-500',
                        },
                    ])
                />
            @endif
            <span
                    @class([
                        'text-sm font-medium',
                        match ($trendColor) {
                            'success' => 'text-success-600 dark:text-success-400',
                            'danger' => 'text-danger-600 dark:text-danger-400',
                            default => 'text-gray-500 dark:text-gray-400',
                        },
                    ])
                >
                    {{ $percentage }}
                </span>
        </div>
    @endif
</div>

{{-- Chart at bottom --}}
@if ($chart = $getChart())
    <div x-data="{ statsOverviewStatChart: function () {} }">
        <div
            @if (FilamentView::hasSpaMode())
                x-load="visible"
            @else
                x-load
            @endif
            x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('stats-overview/stat/chart', 'filament/widgets') }}"
            x-data="statsOverviewStatChart({
                            dataChecksum: @js($dataChecksum),
                            labels: @js(array_keys($chart)),
                            values: @js(array_values($chart)),
                        })"
            @class([
                'fi-wi-stats-overview-stat-chart absolute inset-x-0 bottom-0 overflow-hidden rounded-b-xl',
                match ($chartColor) {
                    'gray' => null,
                    default => 'fi-color-custom',
                },
                is_string($chartColor) ? "fi-color-{$chartColor}" : null,
            ])
            @style([
                \Filament\Support\get_color_css_variables(
                    $chartColor,
                    shades: [50, 400, 500],
                    alias: 'widgets::stats-overview-widget.stat.chart',
                ) => $chartColor !== 'gray',
            ])
        >
            <canvas x-ref="canvas" class="h-6"></canvas>

            <span
                x-ref="backgroundColorElement"
                    @class([
                        match ($chartColor) {
                            'gray' => 'text-gray-100 dark:text-gray-800',
                            default => 'text-custom-50 dark:text-custom-400/10',
                        },
                    ])
                ></span>

            <span
                x-ref="borderColorElement"
                    @class([
                        match ($chartColor) {
                            'gray' => 'text-gray-400',
                            default => 'text-custom-500 dark:text-custom-400',
                        },
                    ])
                ></span>

            <span
                x-ref="gridColorElement"
                class="text-gray-200 dark:text-gray-800"
            ></span>

            <span
                x-ref="textColorElement"
                class="text-gray-500 dark:text-gray-400"
            ></span>
        </div>
    </div>
@endif
</{!! $tag !!}>
