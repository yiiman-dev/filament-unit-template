<x-filament-widgets::widget>
    <x-filament::section heading="فرآیند اجرایی تامین مالی">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10">
                    <thead>
                        <tr>
                            @foreach($this->getStages() as $stage)
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900 whitespace-nowrap border-b border-gray-300 dark:border-white/10">
                                    {{ $stage }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-white/10">
                        @foreach($this->getProcessData() as $index => $process)
                            {{-- ردیف اطلاعات درخواست --}}
                            <tr>
                                <td colspan="{{ count($this->getStages()) }}" class="px-4 py-3 bg-gray-100 dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-100">
                                    <div>
                                        <span class="font-medium">شماره مرجع درخواست اولیه: {{ $process['reference_number'] }}</span>
                                        <span class="text-gray-600 dark:text-gray-400 mr-4">تاریخ درخواست اولیه: {{ $process['date'] }} . ارزش منابع درخواستی {{ $process['value'] }}</span>
                                    </div>
                                </td>
                            </tr>
                            {{-- ردیف وضعیت مراحل --}}
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                @foreach($process['stages'] as $stageStatus)
                                    <td class="px-3 py-4 text-center whitespace-nowrap">
                                        @if($stageStatus === 'success')
                                            <div class="flex justify-center">
                                                <svg class="w-6 h-6 text-green-500 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @elseif($stageStatus === 'failed')
                                            <div class="flex justify-center">
                                                <svg class="w-6 h-6 text-danger-600 dark:text-danger-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @elseif($stageStatus === 'warning')
                                            <div class="flex justify-center">
                                                <svg class="w-6 h-6 text-yellow-500 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-6 h-6"></div>
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

