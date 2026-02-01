# HasUUID Trait

## Description
This trait is used for automatic UUID generation for specified fields in models.

## Location
```
Modules/Basic/app/Concerns/HasUUID.php
```

## Usage

```php
use Modules\Basic\Concerns\HasUUID;

class MyModel extends Model
{
    use HasUUID;
    
    protected static function get_uuid_attributes(): array
    {
        return ['id', 'reference_id'];
    }
}
```

## Methods

### `get_uuid_attributes()`
Define fields that should generate UUID

```php
protected static function get_uuid_attributes(): array
{
    return ['id'];
}
```

### `bootHasUuid()`
Boot method for setting up event listener

```php
protected static function bootHasUuid()
{
    $attributes = self::get_uuid_attributes();
    static::creating(function ($model) use ($attributes) {
        foreach ($attributes as $attr) {
            $model->{$attr} = $model->{$attr} ?? Str::uuid()->toString();
        }
    });
}
```

## Dependencies

- `Illuminate\Support\Str`

## Complete Example

```php
<?php

use Illuminate\Database\Eloquent\Model;
use Modules\Basic\Concerns\HasUUID;

class User extends Model
{
    use HasUUID;
    
    protected $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'reference_id'
    ];
    
    protected static function get_uuid_attributes(): array
    {
        return ['id', 'reference_id'];
    }
}

class Order extends Model
{
    use HasUUID;
    
    protected $fillable = [
        'id',
        'user_id',
        'amount',
        'status',
        'tracking_code'
    ];
    
    protected static function get_uuid_attributes(): array
    {
        return ['id', 'tracking_code'];
    }
}
```

## How it Works

1. **Event Listener**: During model `creating` time, the event listener is activated
2. **Field Check**: Fields defined in `get_uuid_attributes()` are checked
3. **UUID Generation**: If field is empty, a new UUID is generated
4. **Save**: Generated UUID is saved in the field

## Usage Example

```php
// Creating new model
$user = new User([
    'name' => 'Ali Ahmadi',
    'email' => 'ali@example.com',
    'phone' => '09351234567'
]);

// UUID is automatically generated
$user->save();

echo $user->id; // Example: 550e8400-e29b-41d4-a716-4466544000
echo $user->reference_id; // Example: 6ba7b810-9dad-11d1-80b4-00c04fd430c8
```

## Important Notes

1. **Existing Fields**: If field already has a value, it won't change
2. **Field Types**: Field must be of string type
3. **Settings**: Only fields defined in `get_uuid_attributes()` receive UUID
4. **Operation**: Only works during model `creating` time

## Testing

```php
class HasUUIDTest extends TestCase
{
    public function test_uuid_generation()
    {
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
        
        $user->save();
        
        $this->assertNotEmpty($user->id);
        $this->assertNotEmpty($user->reference_id);
        $this->assertIsString($user->id);
        $this->assertIsString($user->reference_id);
    }
    
    public function test_existing_uuid_not_overwritten()
    {
        $existingId = 'existing-uuid-123';
        
        $user = new User([
            'id' => $existingId,
            'name' => 'Test User'
        ]);
        
        $user->save();
        
        $this->assertEquals($existingId, $user->id);
    }
}
```

## Best Practices

1. **Key Fields**: Only use UUID for key fields
2. **Naming**: Use meaningful names for UUID fields
3. **Operation**: UUID is only generated during model creation
4. **Data Types**: Ensure field is of string type

## Benefits

- **Security**: UUIDs are unpredictable
- **Uniqueness**: Very low probability of UUID repetition
- **Distribution**: Ability to generate UUID across multiple servers
- **Privacy**: No disclosure of record count information
