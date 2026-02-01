<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          8/7/25, 4:26 PM
 */

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Units\Auth\My\Models\UserModel;
use Units\Corporates\Placed\Common\Models\CorporateModel;
use Units\Corporates\Users\Common\Models\CorporateUsersModel;

class BasicFilamentTraitsTest extends TestCase
{
//    use RefreshDatabase;
    public function test_interact_with_corporate()
    {

        $user=UserModel::factory()->create();

        $corporate=CorporateModel::factory()->create();
        $corporate_user=CorporateUsersModel::factory()->create();

//        $this->session()->put('');
    }
}
