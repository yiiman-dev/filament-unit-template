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
 * وضعیت‌های ممکن برای تأیید کاربر
 */
enum ValidateStatusEnum: int
{
    /**
     * کاربر تأیید شده است
     */
    case VALIDATED = 1;
    
    /**
     * کاربر تأیید نشده است
     */
    case NOT_VALIDATED = 0;
} 