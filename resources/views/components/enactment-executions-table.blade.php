@php
    use Morilog\Jalali\Jalalian;
    use Units\Enactment\Execution\Common\Enums\FeePaymentTypeEnum;
@endphp

<div class="overflow-x-auto">
    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">شماره اجرا</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاریخ اجرا</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مبلغ اجرا</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مبلغ دریافتی</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">کارمزد اجرا</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نوع پرداخت کارمزد</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">سررسید پرداخت کارمزد</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">شماره مرجع سند</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">وضعیت تسویه</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عودت سند قبلی</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($executions as $execution)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $execution->number ?? '-' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ $execution->date ? Jalalian::fromDateTime($execution->date)->format('%Y/%m/%d') : '-' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ $execution->amount ? number_format($execution->amount) . ' ریال' : '-' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ $execution->amount_received ? number_format($execution->amount_received) . ' ریال' : '-' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ $execution->fee_amount ? number_format($execution->fee_amount) . ' ریال' : '-' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ $execution->type_fee_amount ? FeePaymentTypeEnum::getLabel($execution->type_fee_amount) : '-' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ $execution->fee_amount_deadline ? Jalalian::fromDateTime($execution->fee_amount_deadline)->format('%Y/%m/%d') : '-' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $execution->fee_amount_document_number ?? '-' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($execution->fee_amount_status)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                بله
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                خیر
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($execution->fee_amount_return_document)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                بله
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                خیر
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        هیچ اجرایی ثبت نشده است
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

