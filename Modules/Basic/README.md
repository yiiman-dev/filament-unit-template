# Basic Module

This module contains core functionality and base classes used across the application.

## Structure

### Models
- `APIBaseQueryBuilder`: Base query builder for API operations
- `APIQueryBuilder`: Extended query builder for API operations with Eloquent-like functionality
- `APIModel`: Base model for API operations

### Rules
- `ForbiddenTextRule`: Validation rule for preventing specific text values

## Design Patterns Used

1. **Query Builder Pattern**
   - Implemented in `APIBaseQueryBuilder` and `APIQueryBuilder`
   - Provides fluent interface for building database queries
   - Handles API communication transparently

2. **Repository Pattern**
   - Implemented through API models
   - Abstracts data access layer
   - Provides clean API for data operations

3. **Adapter Pattern**
   - Used in API communication layer
   - Adapts remote API responses to local model structure

## Usage

### API Query Builder
```php
use Modules\Basic\Models\APIQueryBuilder;

$query = new APIQueryBuilder($model);
$results = $query->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->get();
```

### Forbidden Text Rule
```php
use Modules\Basic\Rules\ForbiddenTextRule;

$request->validate([
    'title' => [
        'required',
        new ForbiddenTextRule(['bad_word', 'inappropriate'])
    ]
]);
```

## Integration

This module is integrated with:
- FilamentAdmin module
- FilamentManage module
- Core Laravel functionality

## Dependencies
- Laravel Framework
- Filament
- Guzzle HTTP Client

## API Model

This structure allows you to communicate with other panels through web services instead of connecting directly to the database.

### How to Use

1. Create an API model:

```php
namespace Modules\FilamentAdmin\Models;

use Modules\Basic\Models\APIModel;

class User extends APIModel
{
    protected $table = 'users';
    protected $remoteModel = 'User'; // Name of the model in the target panel
}
```

2. Using the model:

```php
// Get all users
$users = User::all();

// Get user with specific ID
$user = User::find(1);

// Search
$users = User::where('name', 'like', '%John%')->get();

// Sorting
$users = User::orderBy('created_at', 'desc')->get();

// Limit results
$users = User::limit(10)->get();
```

### Features

- Support for all standard Eloquent methods
- Automatic caching for improved performance
- Support for filters and sorting
- Error management

### Design Patterns

This structure uses several design patterns:

1. **Adapter Pattern**: Converting Eloquent requests to API requests
2. **Builder Pattern**: Building API queries using APIQueryBuilder
3. **Proxy Pattern**: Using cache to improve performance

### Related Links

- [APIQueryBuilder](Models/APIQueryBuilder.md)
- [APIModel](Models/APIModel.md)

## Install
Add this module to your project, then update composer.

after that, run this commands:
```bash
php artisan vendor:publish --provider="Mckenziearts\Notify\LaravelNotifyServiceProvider" 

composer dump-autoload
```

!! Dont forget, add new files to Git.


## Add Alert blade component
you can add blade alert on your `view` or `layout` file like this:

```bladehtml
@notifyCss
<x-notify::notify />
@notifyJs
```

### usage:

```php
notify()->success('Welcome to Laravel Notify ⚡️') or notify()->success('Welcome to Laravel Notify ⚡️', 'My custom title')
```
more information on [Author site](https://github.com/mckenziearts/laravel-notify)
