<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          1/27/25, 6:00 PM
 */

namespace FlowTest\Tests;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Units\Corporates\Placed\Common\Models\CorporateModel;
use Units\Corporates\Users\Common\Models\CorporateUsersModel;
use Units\Auth\My\Models\UserModel;
use Units\Auth\My\Enums\ValidateStatusEnum;
use Units\Corporates\Placed\Common\Enums\CorporateVerificationEnum;
use Units\Corporates\Placed\Common\Services\CorporateStatusService;

/**
 * Corporate Form Test
 *
 * Tests the corporate form implementation with status indicators
 *
 * @see Units\Corporates\Placed\Manage\Filament\Resources\CorporateResource\EditCorporate
 * @see Units\Corporates\Placed\Common\Services\CorporateStatusService
 */
class CorporateFormTest extends TestCase
{
    use RefreshDatabase;

    protected array $testData = [
        'corporate' => [
            'national_code' => '۱۴۰۰۱۲۳۴۵۶',
            'corporates_name' => 'تامین کالای ماندگار',
            'manager_national_code' => '۱۴۰۰۱۲۳۴۵۶',
            'manager_name' => 'مسعود مسعودی',
            'manager_mobile' => '۰۹۱۲۱۲۳۴۵۶۷۸',
            'verification_status' => CorporateVerificationEnum::VERIFIED->value,
        ],
        'user' => [
            'national_code' => '۱۴۰۰۱۲۳۴۵۶',
            'phone_number' => '۰۹۱۲۱۲۳۴۵۶۷۸',
            'first_name' => 'مسعود',
            'last_name' => 'مسعودی',
            'validate_status' => ValidateStatusEnum::VALIDATED->value,
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->createTestData();
    }

    /**
     * Test corporate verification status calculation
     */
    public function test_corporate_verification_status()
    {
        $corporate = CorporateModel::where('national_code', $this->testData['corporate']['national_code'])->first();

        $service = new CorporateStatusService();
        $service->actGetCorporateVerificationStatus($corporate);

        $this->assertFalse($service->hasErrors());

        $data = $service->getSuccessResponse()->getData();
        $this->assertEquals(CorporateVerificationEnum::VERIFIED->value, $data['status']);
        $this->assertEquals('تأیید شده', $data['label']);
        $this->assertEquals('success', $data['color']);
    }

    /**
     * Test manager verification status calculation
     */
    public function test_manager_verification_status()
    {
        $corporate = CorporateModel::where('national_code', $this->testData['corporate']['national_code'])->first();

        $service = new CorporateStatusService();
        $service->actGetManagerVerificationStatus($corporate);

        $this->assertFalse($service->hasErrors());

        $data = $service->getSuccessResponse()->getData();
        $this->assertTrue($data['status']);
        $this->assertEquals('تأیید شده', $data['label']);
        $this->assertEquals('success', $data['color']);
    }

    /**
     * Test profile completion status calculation
     */
    public function test_profile_completion_status()
    {
        $corporate = CorporateModel::where('national_code', $this->testData['corporate']['national_code'])->first();

        $service = new CorporateStatusService();
        $service->actGetProfileCompletionStatus($corporate);

        $this->assertFalse($service->hasErrors());

        $data = $service->getSuccessResponse()->getData();
        $this->assertGreaterThan(0, $data['percentage']);
        $this->assertContains($data['status'], ['کامل', 'ناقص', 'پایه']);
    }

    /**
     * Test all statuses together
     */
    public function test_all_statuses()
    {
        $corporate = CorporateModel::where('national_code', $this->testData['corporate']['national_code'])->first();

        $service = new CorporateStatusService();
        $service->actGetAllStatuses($corporate);

        $this->assertFalse($service->hasErrors());

        $data = $service->getSuccessResponse()->getData();
        $this->assertArrayHasKey('corporate_verification', $data);
        $this->assertArrayHasKey('manager_verification', $data);
        $this->assertArrayHasKey('profile_completion', $data);
    }

    /**
     * Create test data
     */
    private function createTestData()
    {
        // Create user
        $user = UserModel::create($this->testData['user']);

        // Create corporate
        $corporate = CorporateModel::create($this->testData['corporate']);

        // Create corporate user relationship
        CorporateUsersModel::create([
            'corporate_national_code' => $corporate->national_code,
            'user_id' => $user->id,
            'rule_of_user' => 'ceo',
        ]);
    }
}


