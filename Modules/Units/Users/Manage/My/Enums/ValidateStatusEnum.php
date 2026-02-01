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

enum ValidateStatusEnum: int
{
    /**
     * User is not validated
     */
    case NOT_VALIDATED = 0;
    
    /**
     * User is validated
     */
    case VALIDATED = 1;
} 