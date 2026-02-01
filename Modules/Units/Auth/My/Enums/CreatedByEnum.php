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
 * منابع ایجاد کاربر
 */
enum CreatedByEnum: string
{
    /**
     * ایجاد شده توسط سیستم
     */
    case SYSTEM = 'system';
    
    /**
     * ایجاد شده توسط ادمین
     */
    case ADMIN = 'admin';
    
    /**
     * ایجاد شده توسط کاربر
     */
    case USER = 'user';
} 