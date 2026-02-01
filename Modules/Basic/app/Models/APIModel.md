# کلاس APIModel

این کلاس پایه برای ایجاد مدل‌های API است که به جای اتصال مستقیم به دیتابیس، از طریق وب‌سرویس با پنل‌های دیگر ارتباط برقرار می‌کنند.

## ویژگی‌ها

- `apiBaseUrl`: آدرس پایه API
- `remoteModel`: نام مدل در پنل مقصد
- `cacheTtl`: زمان انقضای کش (به ثانیه)

## متدها

### `find($id)`
دریافت رکورد با ID مشخص

### `all()`
دریافت تمام رکوردها

### `save()`
ذخیره مدل

### `update()`
به‌روزرسانی مدل

### `delete()`
حذف مدل

## دیزاین پترن‌ها

### Adapter Pattern
این کلاس از Adapter Pattern استفاده می‌کند تا رابط Eloquent Model را به رابط API تبدیل کند.

### Proxy Pattern
استفاده از کش برای بهبود عملکرد و کاهش درخواست‌های API.

## مثال استفاده

```php
namespace Modules\FilamentAdmin\Models;

use Modules\Basic\Models\APIModel;

class User extends APIModel
{
    protected $table = 'users';
    protected $remoteModel = 'User';
}
``` 