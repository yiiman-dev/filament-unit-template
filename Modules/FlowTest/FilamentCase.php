<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/30/25, 3:17 PM
 */

namespace FlowTest;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Units\Auth\Admin\Models\UserModel as AdminUser;
use Units\Auth\Manage\Models\UserModel as ManageUser;
use Units\Auth\My\Models\UserModel as MyUser;

class FilamentCase extends TestCase
{
    use RefreshDatabase;

    /**
     * ورود به عنوان کاربر ادمین
     * @param string|null $username شماره موبایل کاربر
     * @return $this
     */
    protected function actingAsAdmin(?string $username = null): self
    {
        $user = $username ? AdminUser::where('phone_number', $username)->firstOrFail() 
            : AdminUser::factory()->create();
            
        $this->actingAs($user, 'admin');
        return $this;
    }

    /**
     * ورود به عنوان کاربر مدیر
     * @param string|null $username شماره موبایل کاربر
     * @return $this
     */
    protected function actingAsManage(?string $username = null): self
    {
        $user = $username ? ManageUser::where('phone_number', $username)->firstOrFail() 
            : ManageUser::factory()->create();
            
        $this->actingAs($user, 'manage');
        return $this;
    }

    /**
     * ورود به عنوان کاربر عادی
     * @param string|null $username شماره موبایل کاربر
     * @return $this
     */
    protected function actingAsMy(?string $username = null): self
    {
        $user = $username ? MyUser::where('phone_number', $username)->firstOrFail() 
            : MyUser::factory()->create();
            
        $this->actingAs($user, 'my');
        return $this;
    }
}
