<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/8/25, 7:57 PM
 */

namespace Units\Users\Manage\My\Enums;

enum UserStatusEnum: int
{
    /**
     * User is active
     */
    case ACTIVE = 1;
    
    /**
     * User is inactive
     */
    case INACTIVE = 0;
} 