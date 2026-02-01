<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          5/2/25, 7:30 PM
 */

namespace Units\Auth\My\Enums;

/**
 * وضعیت‌های ممکن برای کاربر
 */
enum UserStatusEnum: int
{
    /**
     * کاربر فعال است
     */
    case ACTIVE = 1;
    
    /**
     * کاربر غیرفعال است
     */
    case INACTIVE = 0;
} 