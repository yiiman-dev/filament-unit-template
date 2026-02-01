<x-filament-widgets::widget>
    <x-filament::section heading="پیگیری وضعیت فرآیند">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <table
                    class="min-w-full divide-y divide-gray-200 dark:divide-white/10 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <thead>
                    <tr>
                        @foreach($this->getStages() as $stage)

                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900 whitespace-nowrap border-b border-gray-300 dark:border-white/10">
                                <div class="flex items-center justify-center gap-2">
                                    @if($stage['number']!=0)
                                        <div
                                            class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-sm font-bold text-gray-700 dark:text-gray-300">
                                            {{ $stage['number'] }}
                                        </div>
                                        <span>{{ $stage['name'] }}</span>
                                    @endif

                                </div>
                            </th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-white/10">
                    @foreach($this->getProcessData() as $index => $process)
                        <tr>
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                <div class="text-right text-sm text-gray-900 dark:text-gray-100">
                                    <div class="text-gray-600 dark:text-gray-400 text-xs">
                                        <span ><b>          درخواست اولیه:
                                            {{ $process['request_id'] }}
                                            </b></span>
                                        <br>
                                        <br>
                                        {{ $process['date'] }} : {{ $process['value'] }}
                                    </div>
                                </div>
                            </td>
                            @foreach($process['stages'] as $stageIndex => $stageData)
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    @if($stageData)
                                        @php
                                            $statusInfo = $this->getStatusInfo($stageData['status']);
                                        @endphp
                                        <div class="flex flex-col items-center gap-2">
                                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium
                                                        @if($statusInfo['color'] === 'green')
                                                            bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                                        @elseif($statusInfo['color'] === 'yellow')
                                                            bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                                                        @elseif($statusInfo['color'] === 'red')
                                                            bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                                                        @endif
                                                    ">
                                                        {{ $statusInfo['label'] }}
                                                    </span>
                                            <span class="text-xs text-gray-600 dark:text-gray-400">
                                                        {{ $stageData['datetime'] }}
                                                    </span>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

