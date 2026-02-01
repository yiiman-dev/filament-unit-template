<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/28/25, 12:00 PM
 */

namespace FlowTest\CorporateManagement;

use Enums\ActivationEnum;
use Filament\Livewire\Edit;
use FlowTest\FilamentCase;
use Livewire\Livewire;
use Units\Corporates\Placed\Common\Models\CorporateModel;
use Units\Corporates\Placed\Manage\Filament\Resources\CorporateResource\EditCorporate;
use Units\Corporates\Placed\Manage\Services\CorporateService;

/**
 * تست‌های مربوط به تأیید و رد شرکت‌ها
 *
 * @see \Units\Corporates\Placed\Manage\Services\CorporateService
 * @see \Units\Corporates\Placed\Manage\Filament\Resources\CorporateResources
 * @see \Units\Corporates\Placed\Manage\Filament\Resources\CorporateResource\EditCorporate
 */
class CorporateApprovalTest extends FilamentCase
{
    protected array $testData = [
        'corporate' => [
            'national_code' => '14001234567',
            'corporates_name' => 'شرکت تست',
            'manager_name' => 'محمد محمدی',
            'manager_national_code' => '1234567890',
            'manager_mobile' => '09123456789',
            'birth_date' => '1399-01-01',
            'registration_number' => '12345678901',
            'field_of_activity' => 'manufacturing',
            'activity_summary' => 'تولید قطعات صنعتی',
            'central_office_telephone' => '02188776655',
            'central_office_postal_code' => '1234567890',
            'central_office_address' => 'تهران، خیابان ولیعصر، پلاک ۱۲۳',
            'status' => ActivationEnum::DEACTIVATE->value,
        ]
    ];

    protected function setUp(): void
    {
        parent::setUp();

        // ایجاد یک شرکت تست
        $this->testCorporate = CorporateModel::factory()->create($this->testData['corporate']);
    }

    /**
     * تست تأیید شرکت با ارسال SMS
     *
     * @test
     */
    public function it_can_approve_corporate_with_sms()
    {
        // Given: یک شرکت در انتظار تأیید
        $this->assertDatabaseHas('corporates', [
            'national_code' => $this->testData['corporate']['national_code'],
            'status' => ActivationEnum::DEACTIVATE->value
        ]);

        // When: تأیید شرکت با SMS
        $service = app(CorporateService::class);
        $result = $service->actApproveWithSms(
            $this->testData['corporate']['national_code'],
            [],
            true
        );

        // Then: شرکت تأیید شده و پاسخ موفق دریافت شده
        $this->assertFalse($result->hasErrors());
        $this->assertDatabaseHas('corporates', [
            'national_code' => $this->testData['corporate']['national_code'],
            'status' => ActivationEnum::ACTIVE->value
        ]);

        $responseData = $result->getSuccessResponse()->getData();
        $this->assertEquals($this->testData['corporate']['national_code'], $responseData['corporate_national_code']);
        $this->assertEquals('approved', $responseData['status']);
        $this->assertTrue($responseData['sms_sent']);
    }

    /**
     * تست رد شرکت با دلیل و SMS
     *
     * @test
     */
    public function it_can_reject_corporate_with_reason_and_sms()
    {
        // Given: یک شرکت فعال
        $this->testCorporate->update(['status' => ActivationEnum::ACTIVE->value]);

        // When: رد شرکت با دلیل
        $reason = 'مدارک ناکافی';
        $service = app(CorporateService::class);
        $result = $service->actRejectWithSms(
            $this->testData['corporate']['national_code'],
            $reason,
            true
        );

        // Then: شرکت رد شده و دلیل ثبت شده
        $this->assertFalse($result->hasErrors());
        $this->assertDatabaseHas('corporates', [
            'national_code' => $this->testData['corporate']['national_code'],
            'status' => ActivationEnum::DEACTIVATE->value
        ]);

        $responseData = $result->getSuccessResponse()->getData();
        $this->assertEquals($this->testData['corporate']['national_code'], $responseData['corporate_national_code']);
        $this->assertEquals('rejected', $responseData['status']);
        $this->assertEquals($reason, $responseData['reason']);
        $this->assertTrue($responseData['sms_sent']);
    }

    /**
     * تست فرم ویرایش شرکت در Filament
     *
     * @test
     */
    public function it_can_edit_corporate_form_in_filament()
    {
        // Given: ورود به عنوان کاربر مدیر
        $this->actingAsManageUser();

        // When: ورود به صفحه ویرایش شرکت
        $component = Livewire::test(
            EditCorporate::class,
            ['record' => $this->testCorporate->national_code]
        );

        // Then: فرم با اطلاعات صحیح بارگذاری شده
        $component->assertSet('data.national_code', $this->testData['corporate']['national_code']);
        $component->assertSet('data.corporates_name', $this->testData['corporate']['corporates_name']);
        $component->assertSet('data.manager_name', $this->testData['corporate']['manager_name']);
        $component->assertSet('data.manager_mobile', $this->testData['corporate']['manager_mobile']);
    }

    /**
     * تست اکشن تأیید از طریق UI
     *
     * @test
     */
    public function it_can_approve_corporate_through_ui()
    {
        // Given: ورود به عنوان کاربر مدیر
        $this->actingAsManageUser();

        // When: اجرای اکشن تأیید
        $component = Livewire::test(
            EditCorporate::class,
            ['record' => $this->testCorporate->national_code]
        );

        $component->callAction('approve', [
            'send_sms' => true
        ]);

        // Then: شرکت تأیید شده و notification نمایش داده شده
        $this->assertDatabaseHas('corporates', [
            'national_code' => $this->testData['corporate']['national_code'],
            'status' => ActivationEnum::ACTIVE->value
        ]);

        $component->assertNotified('شرکت تأیید شد');
    }

    /**
     * تست اکشن رد از طریق UI
     *
     * @test
     */
    public function it_can_reject_corporate_through_ui()
    {
        // Given: ورود به عنوان کاربر مدیر و شرکت فعال
        $this->actingAsManageUser();
        $this->testCorporate->update(['status' => ActivationEnum::ACTIVE->value]);

        // When: اجرای اکشن رد
        $component = Livewire::test(
            EditCorporate::class,
            ['record' => $this->testCorporate->national_code]
        );

        $component->callAction('reject', [
            'reason' => 'مدارک ناقص',
            'send_sms' => true
        ]);

        // Then: شرکت رد شده و notification نمایش داده شده
        $this->assertDatabaseHas('corporates', [
            'national_code' => $this->testData['corporate']['national_code'],
            'status' => ActivationEnum::DEACTIVATE->value
        ]);

        $component->assertNotified('شرکت رد شد');
    }

    /**
     * تست validation های فرم
     *
     * @test
     */
    public function it_validates_corporate_form_data()
    {
        // Given: ورود به عنوان کاربر مدیر
        $this->actingAsManageUser();

        // When: ارسال فرم با داده‌های نامعتبر
        $component = Livewire::test(
            EditCorporate::class,
            ['record' => $this->testCorporate->national_code]
        );

        $component->set('data.national_code', '123'); // کد ملی نامعتبر
        $component->set('data.manager_mobile', '123456'); // شماره موبایل نامعتبر
        $component->set('data.central_office_postal_code', '123'); // کدپستی نامعتبر

        $component->call('save');

        // Then: خطاهای validation نمایش داده می‌شود
        $component->assertHasErrors([
            'data.national_code',
            'data.manager_mobile',
            'data.central_office_postal_code'
        ]);
    }

    protected function tearDown(): void
    {
        // پاک کردن داده‌های تست
        if (isset($this->testCorporate)) {
            $this->testCorporate->forceDelete();
        }

        parent::tearDown();
    }
}
