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

namespace Modules\Basic\BaseKit\Filament\Form\Components;

use Filament\Forms\Components\Component;

/**
 * ColumnComponent
 *
 * کامپوننت ستون با رفتار فلکس باکس (column) برای فرم‌ها
 * این کامپوننت عناصر فرزند را در یک ستون عمودی نمایش می‌دهد
 * و از Tailwind CSS برای استایل‌دهی استفاده می‌کند
 *
 * @see ColumnComponentTest
 * @see resources/views/components/column.blade.php
 */
class ColumnComponent extends Component
{
    protected string $view = 'components.column';

    /**
     * ایجاد نمونه جدید از کامپوننت ستون
     *
     * @param array $children عناصر فرزند که در ستون نمایش داده می‌شوند
     * @param array $attributes ویژگی‌های اضافی برای شخصی‌سازی
     * @return static
     */
    public static function make(array $children = [], array $attributes = []): static
    {
        $instance = new static();
        $instance->withViewData([
            'children' => $children,
            'attributes' => $attributes,
        ]);
        return $instance;
    }
}
