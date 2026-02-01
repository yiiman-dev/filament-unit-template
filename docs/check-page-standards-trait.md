# CheckPageStandards Trait

## توضیحات
این trait برای بررسی استانداردهای توسعه و اطمینان از رعایت قوانین کدنویسی استفاده می‌شود.

## محل قرارگیری
```
Modules/Basic/app/BaseKit/Filament/Concerns/CheckPageStandards.php
```

## نحوه استفاده

```php
use Modules\Basic\BaseKit\Filament\Concerns\CheckPageStandards;

/**
 * @url https://www.figma.com/file/example
 */
class MyFilamentPage extends Page
{
    use CheckPageStandards;
    
    public function mount()
    {
        $this->checkDevelopentStandards();
    }
}
```

## متدها

### `checkDevelopentStandards()`
بررسی استانداردهای توسعه

```php
public function checkDevelopentStandards()
{
    if (app()->hasDebugModeEnabled()) {
        $this->checkClassCommentStandards();
    }
}
```

### `checkClassCommentStandards()`
بررسی استانداردهای کامنت کلاس

```php
protected function checkClassCommentStandards()
{
    // Get the child class name
    $childClass = get_called_class();

    // Use Reflection to inspect the child class
    $reflection = new \ReflectionClass($childClass);

    // Get the doc comment (if any) from the child class
    $docComment = $reflection->getDocComment();

    // Strings to check for in the doc comment
    $requiredStrings = ['@url https://www.figma.com'];

    // If no doc comment exists, raise an exception
    if (!$docComment) {
        throw new \Exception("The child class '$childClass' must have a doc comment.");
    }

    // Check if all required strings are present in the doc comment
    foreach ($requiredStrings as $string) {
        if (strpos($docComment, $string) === false) {
            throw new \Exception(
                "The child class '$childClass' is missing the required string '$string' in its doc comment."
            );
        }
    }
}
```

## وابستگی‌ها

- `\ReflectionClass`

## مثال کامل

```php
<?php

use Filament\Pages\Page;
use Modules\Basic\BaseKit\Filament\Concerns\CheckPageStandards;

/**
 * صفحه داشبورد کاربر
 * 
 * @url https://www.figma.com/file/dashboard-user
 * @author Saman beheshtian
 * @version 1.0
 */
class UserDashboardPage extends Page
{
    use CheckPageStandards;
    
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.user-dashboard';
    
    public function mount()
    {
        $this->checkDevelopentStandards();
    }
    
    public function render()
    {
        return view(static::$view);
    }
}

/**
 * صفحه مدیریت کاربران
 * 
 * @url https://www.figma.com/file/user-management
 * @author Saman beheshtian
 * @version 1.0
 */
class UserManagementPage extends Page
{
    use CheckPageStandards;
    
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static string $view = 'filament.pages.user-management';
    
    public function mount()
    {
        $this->checkDevelopentStandards();
    }
}
```

## استانداردهای مورد بررسی

### 1. وجود کامنت کلاس
کلاس باید دارای کامنت باشد:

```php
/**
 * توضیحات کلاس
 */
class MyClass
{
    // ...
}
```

### 2. وجود لینک Figma
کامنت کلاس باید شامل لینک Figma باشد:

```php
/**
 * @url https://www.figma.com/file/example
 */
class MyClass
{
    // ...
}
```

## نحوه کارکرد

1. **بررسی Debug Mode**: بررسی فقط در حالت debug انجام می‌شود
2. **Reflection**: از Reflection برای بررسی کلاس استفاده می‌شود
3. **کامنت**: کامنت کلاس بررسی می‌شود
4. **لینک Figma**: وجود لینک Figma در کامنت بررسی می‌شود
5. **خطا**: در صورت عدم رعایت استانداردها، exception پرتاب می‌شود

## مثال کامنت صحیح

```php
/**
 * صفحه مدیریت محصولات
 * 
 * این صفحه برای مدیریت محصولات استفاده می‌شود
 * 
 * @url https://www.figma.com/file/product-management
 * @author Saman beheshtian
 * @version 1.0
 * @since 2025-01-01
 */
class ProductManagementPage extends Page
{
    use CheckPageStandards;
    
    public function mount()
    {
        $this->checkDevelopentStandards();
    }
}
```

## نکات مهم

1. **Debug Mode**: بررسی فقط در حالت debug انجام می‌شود
2. **کامنت الزامی**: هر کلاس باید کامنت داشته باشد
3. **لینک Figma**: لینک Figma در کامنت الزامی است
4. **Exception**: در صورت عدم رعایت استانداردها، exception پرتاب می‌شود

## تست

```php
class CheckPageStandardsTest extends TestCase
{
    public function test_valid_class_with_figma_url()
    {
        $page = new ValidPage();
        
        // Should not throw exception
        $page->checkDevelopentStandards();
        
        $this->assertTrue(true);
    }
    
    public function test_invalid_class_without_figma_url()
    {
        $this->expectException(\Exception::class);
        
        $page = new InvalidPage();
        $page->checkDevelopentStandards();
    }
}

/**
 * @url https://www.figma.com/file/valid
 */
class ValidPage extends Page
{
    use CheckPageStandards;
}

/**
 * Invalid page without Figma URL
 */
class InvalidPage extends Page
{
    use CheckPageStandards;
}
```

## بهترین شیوه‌ها

1. **کامنت کامل**: از کامنت‌های کامل و واضح استفاده کنید
2. **لینک Figma**: همیشه لینک Figma را در کامنت قرار دهید
3. **Debug Mode**: این trait فقط در حالت debug بررسی می‌کند
4. **مستندات**: کامنت‌ها را به‌روز نگه دارید

## مزایا

- **استانداردسازی**: اطمینان از رعایت استانداردهای کدنویسی
- **مستندات**: اطمینان از وجود مستندات مناسب
- **طراحی**: اطمینان از وجود لینک طراحی
- **نگهداری**: بهبود قابلیت نگهداری کد
