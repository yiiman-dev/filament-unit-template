<?php
/**
 * @var \Units\BusinessCoworkers\Common\Models\BusinessCoWorkerModel $record
 * @var array $data
 */
?>

<div class="space-y-4">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-blue-80 mb-2">تایید نهایی اطلاعات</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
                <div class="text-sm text-gray-600">شناسه ملی بنگاه مقصد:</div>
                <div class="font-medium">{{ $data['corporate_national_code_destination'] ?? 'نامشخص' }}</div>
            </div>

            <div class="space-y-2">
                <div class="text-sm text-gray-600">نام بنگاه:</div>
                <div class="font-medium">{{ $data['destination_corporate_name'] ?? ($data['new_corporate_name'] ?? 'نامشخص') }}</div>
            </div>

            <div class="space-y-2">
                <div class="text-sm text-gray-600">نوع رابطه:</div>
                <div class="font-medium">
                    @if(isset($data['relation_types']) && is_array($data['relation_types']))
                        @foreach($data['relation_types'] as $type)
                            <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mr-1 mb-1">
                                {{ \Units\BusinessCoworkers\Common\Enums\BusinessCoWorkerTypesEnum::getLabel($type) }}
                            </span>
                        @endforeach
                    @endif
                </div>

            <div class="space-y-2">
                <div class="text-sm text-gray-600">توضیحات:</div>
                <div class="font-medium">{{ $data['description'] ?? 'بدون توضیحات' }}</div>
            </div>
        </div>

        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-20 rounded">
            <p class="text-sm text-yellow-800">
                <strong>توجه:</strong>
                @if(config('bcw.require_opposite_party_approval', true))
                    این رابطه پس از ثبت، نیاز به تأیید بنگاه مقابل دارد.
                @else
                    این رابطه به‌صورت خودکار تأید خواهد شد.
                @endif
            </p>
        </div>
    </div>
</div>
