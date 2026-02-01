# InteractWithCorporate Trait

## Description
This trait is used for interacting with corporations and managing related information.

## Location
```
Modules/Basic/app/BaseKit/Filament/InteractWithCorporate.php
```

## Usage

```php
use Modules\Basic\BaseKit\Filament\InteractWithCorporate;

class MyFilamentPage extends Page
{
    use InteractWithCorporate;
    
    public function getCorporateInfo()
    {
        $nationalCode = $this->getCorporateNationalCode();
        $corporate = $this->getCorporateModel();
        $users = $this->getCorporateUsers('ceo');
        $ceo = $this->getCorporateCEOModel();
    }
}
```

## Methods

### `getCorporateNationalCode()`
Get corporate national code from session

```php
public function getCorporateNationalCode()
{
    return session()->get('corporate_national_code');
}
```

### `getCorporateModel()`
Get corporate model based on national code

```php
public function getCorporateModel(): CorporateModel|null
{
    return CorporateModel::first([
        'national_code' => $this->getCorporateNationalCode()
    ]);
}
```

### `getCorporateUsers($role = null)`
Get corporate users with or without role filter

```php
public function getCorporateUsers($role = null): \Illuminate\Database\Eloquent\Collection
{
    $query = CorporateUsersModel::where('national_code', $this->getCorporateNationalCode());
    if ($role) {
        $query->where('rule_of_user', $role);
    }
    return $query->get();
}
```

### `getCorporateCEOModel()`
Get corporate CEO model

```php
public function getCorporateCEOModel(): UserModel|null
{
    $corporate_user_collection = $this->getCorporateUsers('ceo');
    if (!empty($corporate_user_model = $corporate_user_collection->first())) {
        $user_id = $corporate_user_model->user_id;
        return UserModel::first(['id' => $user_id]);
    }
    return null;
}
```

## Dependencies

- `Units\Auth\My\Models\UserModel`
- `Units\Corporates\Placed\Common\Models\CorporateModel`
- `Units\Corporates\Users\Common\Models\CorporateUsersModel`

## Complete Example

```php
<?php

use Filament\Pages\Page;
use Modules\Basic\BaseKit\Filament\InteractWithCorporate;
use Modules\Basic\BaseKit\Filament\HasNotification;

class CorporateDashboardPage extends Page
{
    use InteractWithCorporate, HasNotification;
    
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static string $view = 'filament.pages.corporate-dashboard';
    
    public function getCorporateStatistics()
    {
        $corporate = $this->getCorporateModel();
        
        if (!$corporate) {
            $this->alert_error('Corporate not found');
            return;
        }
        
        $users = $this->getCorporateUsers();
        $ceo = $this->getCorporateCEOModel();
        
        return [
            'corporate' => $corporate,
            'total_users' => $users->count(),
            'ceo' => $ceo
        ];
    }
    
    public function getCEOContact()
    {
        $ceo = $this->getCorporateCEOModel();
        
        if ($ceo) {
            return [
                'name' => $ceo->name,
                'phone' => $ceo->phone_number,
                'email' => $ceo->email
            ];
        }
        
        return null;
    }
    
    public function getUsersByRole($role)
    {
        return $this->getCorporateUsers($role);
    }
}
```

## User Roles

| Role | Description |
|-----|---------|
| `ceo` | Chief Executive Officer |
| `manager` | Manager |
| `employee` | Employee |
| `admin` | System Administrator |

## Usage Example

```php
// Get all corporate users
$allUsers = $this->getCorporateUsers();

// Get only managers
$managers = $this->getCorporateUsers('manager');

// Get CEO
$ceo = $this->getCorporateCEOModel();

// Get corporate information
$corporate = $this->getCorporateModel();
```

## Important Notes

1. **Session**: Corporate national code is read from session
2. **Roles**: User roles must be defined in database
3. **CEO**: Only users with 'ceo' role are recognized as CEO
4. **Error**: Returns null if corporate doesn't exist

## Testing

```php
class InteractWithCorporateTest extends TestCase
{
    public function test_get_corporate_national_code()
    {
        session(['corporate_national_code' => '1234567890']);
        
        $page = new CorporateDashboardPage();
        $nationalCode = $page->getCorporateNationalCode();
        
        $this->assertEquals('1234567890', $nationalCode);
    }
    
    public function test_get_corporate_model()
    {
        // Mock CorporateModel
        $corporate = CorporateModel::factory()->create([
            'national_code' => '1234567890'
        ]);
        
        session(['corporate_national_code' => '1234567890']);
        
        $page = new CorporateDashboardPage();
        $result = $page->getCorporateModel();
        
        $this->assertEquals($corporate->id, $result->id);
    }
}
```

## Best Practices

1. **Existence Check**: Always check corporate existence
2. **Valid Roles**: Use valid roles
3. **Error Handling**: Handle potential errors
4. **Caching**: Use caching for performance improvement
