<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'نام',
    'column.guard_name' => 'نام گارد',
    'column.roles' => 'نقش‌ها',
    'column.permissions' => 'دسترسی‌ها',
    'column.updated_at' => 'به‌روزشده در',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'نام',
    'field.guard_name' => 'نام گارد',
    'field.permissions' => 'دسترسی‌ها',
    'field.select_all.name' => 'انتخاب همه',
    'field.select_all.message' => 'تمام دسترسی‌های <span class="text-primary font-medium">فعال</span> فعلی را برای این نقش فعال کن.',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'مدیریت کاربران',
    'nav.role.label' => 'نقش های پنل تامین مالی',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'نقش',
    'resource.label.roles' => 'نقش‌ها',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'موجودیت‌ها',
    'resources' => 'منابع',
    'widgets' => 'ویجت‌ها',
    'pages' => 'صفحات',
    'custom' => 'دسترسی‌های سفارشی',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'شما اجازه دسترسی ندارید.',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'نمایش',
        'view_any' => 'نمایش همه',
        'create' => 'ایجاد',
        'update' => 'ویرایش',
        'delete' => 'حذف',
        'delete_any' => 'حذف همه',
        'force_delete' => 'حذف اجباری',
        'force_delete_any' => 'حذف اجباری همه',
        'restore' => 'بازیابی',
        'replicate' => 'تکثیر',
        'reorder' => 'مرتب‌سازی',
        'restore_any' => 'بازیابی همه',
        'finance_request_admin_of_manage_first_approve'=>'پذیرش درخواست اولیه ی تامین مالی',
        'finance_request_admin_of_manage_first_reject_finance'=>'رد درخواست اولیه ی تامین مالی',
        'finance_operation_feasibility_edit' => 'ویرایش امکان‌سنجی عملیاتی',
        'finance_operation_submit_decision' => 'ارسال تصمیم گیری عملیاتی',
        'finance_team_manager_final_decision' => 'تصمیم نهایی مدیر تیم',
        'finance_contract_proceed' => 'پیشبرد قرارداد',
        'finance_contract_management' => 'مدیریت قرارداد',
    ],
];
