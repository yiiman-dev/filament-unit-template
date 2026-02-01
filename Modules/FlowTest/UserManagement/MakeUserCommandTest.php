<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/30/25, 3:13 PM
 */

namespace FlowTest\UserManagement;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Modules\Basic\Helpers\Helper;
use Tests\TestCase;
use Units\Users\Admin\Admin\Services\UserService;

/**
 * تست دستور ساخت کاربر فیلامنت
 */
class MakeUserCommandTest extends TestCase
{
    use RefreshDatabase;

    protected UserService $userService;

    protected function setUp(): void
    {

        parent::setUp();
        $this->userService = new UserService();
    }

    /**
     * داده های مورد نیاز برای تست
     */
    protected array $data = [
        'valid_user' => [
            'username' => 'admin',
            'mobile' => '09123456789',
            'password' => 'password10@'
        ],
        'existing_user' => [
            'username' => 'existinguser',
            'mobile' => '09187654321',
            'password' => 'password123'
        ],
        'invalid_mobile' => [
            'username' => 'invaliduser',
            'mobile' => '123', // شماره موبایل نامعتبر
            'password' => 'password123'
        ]
    ];

    /**
     * تست ساخت کاربر با داده های معتبر
     */
    public function test_can_create_user_with_valid_data(): void
    {
        $exitCode = Artisan::call('make:filament-user', [
            '--username' => $this->data['valid_user']['username'],
            '--mobile' => $this->data['valid_user']['mobile'],
            '--password' => $this->data['valid_user']['password']
        ]);

        $this->assertEquals(0, $exitCode);

        $user = $this->userService->getByUserName($this->data['valid_user']['username']);

        $this->assertNotNull($user, 'کاربر در دیتابیس ایجاد نشده است');
        $this->assertEquals(
            Helper::normalize_phone_number($this->data['valid_user']['mobile']),
            $user->phone_number,
            'شماره موبایل به درستی نرمالایز نشده است'
        );
        $this->assertEquals($this->data['valid_user']['username'], $user->username);
    }

    /**
     * تست جلوگیری از ساخت کاربر تکراری
     */
    public function test_cannot_create_duplicate_user(): void
    {
        // ابتدا یک کاربر می سازیم
        $this->userService->actCreate(
            $this->data['existing_user']['username'],
            $this->data['existing_user']['password'],
            $this->data['existing_user']['mobile'],
            UserService::STATUS_ACTIVE,
            UserService::CREATED_BY_SYSTEM
        );

        $this->assertFalse($this->userService->hasErrors(), 'خطا در ایجاد کاربر اولیه');

        // تلاش برای ساخت کاربر تکراری
        $exitCode = Artisan::call('make:filament-user', [
            '--username' => $this->data['existing_user']['username'],
            '--mobile' => $this->data['existing_user']['mobile'],
            '--password' => $this->data['existing_user']['password']
        ]);

        $this->assertEquals(1, $exitCode);
    }

    /**
     * تست اعتبارسنجی شماره موبایل نامعتبر
     */
    public function test_validates_invalid_mobile(): void
    {
        $exitCode = Artisan::call('make:filament-user', [
            '--username' => $this->data['invalid_mobile']['username'],
            '--mobile' => $this->data['invalid_mobile']['mobile'],
            '--password' => $this->data['invalid_mobile']['password']
        ]);

        $this->assertEquals(1, $exitCode);

        $user = $this->userService->getByUserName($this->data['invalid_mobile']['username']);
        $this->assertNull($user, 'کاربر با موبایل نامعتبر نباید در دیتابیس ذخیره شود');
    }
}
