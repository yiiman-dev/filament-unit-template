# کلاس APIQueryBuilder

این کلاس برای ساخت و اجرای کوئری‌های API استفاده می‌شود.

## ویژگی‌ها

- `model`: مدل مربوطه
- `queryParams`: پارامترهای کوئری

## متدها

### `where($column, $operator, $value)`
اضافه کردن شرط where به کوئری

### `orderBy($column, $direction)`
مرتب‌سازی نتایج

### `limit($limit)`
محدود کردن تعداد نتایج

### `first()`
دریافت اولین نتیجه

### `get()`
دریافت تمام نتایج

### `count()`
شمارش تعداد نتایج

## دیزاین پترن‌ها

### Builder Pattern
این کلاس از Builder Pattern استفاده می‌کند تا امکان ساخت کوئری‌های پیچیده را به صورت زنجیره‌ای فراهم کند.

## مثال استفاده

```php
$users = User::where('name', 'like', '%John%')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();
``` 