# Panel Creation Guide

This guide explains how to create new panels in the Filament project following the established architecture patterns.

## Overview

Panels in this project follow a modular structure where each domain unit can have multiple panels (My, Manage, Admin) with shared components in a Common directory. This architecture ensures code reusability and maintains separation of concerns.

## Directory Structure

```
Modules/Units/{DomainName}/
├── Common/                    # Shared components across all panels
│   ├── Models/               # Eloquent models
│   ├── Enums/                # Enum classes
│   ├── DTO/                  # Data Transfer Objects
│   ├── Repository/           # Repository classes
│   ├── Filament/             # Shared Filament components
│   │   ├── Tables/           # Table schemas
│   │   ├── Forms/            # Form schemas
│   │   └── Resources/        # Shared resources
│   └── database/             # Migrations and seeders
├── My/                       # My panel (user-facing)
│   ├── {DomainName}MyPlugin.php
│   └── Filament/
│       ├── Resources/        # My panel resources
│       └── Schematic/        # My panel schematics
├── Manage/                    # Manage panel (management interface)
│   ├── {DomainName}ManagePlugin.php
│   └── Filament/
│       ├── Initial/          # Initial management resources
│       │   ├── Resources/
│       │   └── Schematic/
│       └── Advanced/         # Advanced management resources
└── Admin/                     # Admin panel (administrative interface)
    ├── {DomainName}AdminPlugin.php
    └── Filament/
        ├── Resources/
        └── Schematic/

```

## Step-by-Step Panel Creation

### 1. Create Domain Directory Structure

First, create the main domain directory under `Modules/Units/`:

```bash
mkdir -p Modules/Units/{YourDomain}/Common/{Models,Enums,DTO,Repository,Filament/{Tables,Forms,Resources},database}
mkdir -p Modules/Units/{YourDomain}/My/Filament/{Resources,Schematic}
mkdir -p Modules/Units/{YourDomain}/Manage/Filament/Initial/{Resources,Schematic}
mkdir -p Modules/Units/{YourDomain}/Admin/Filament/{Resources,Schematic}
```

### 2. Create Plugin Classes

#### My Panel Plugin

```php
<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/28/25, 8:56 AM
 */

namespace Units\{YourDomain}\My;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Units\{YourDomain}\My\Filament\Resources\{YourDomain}Resource;

class {YourDomain}MyPlugin implements Plugin
{
    public static function make()
    {
        return new static();
    }

    public function getId(): string
    {
        return 'filament-{your-domain}-my-plugin';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                {YourDomain}Resource::class
            ]);
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
```

#### Manage Panel Plugin

```php
<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/28/25, 8:56 AM
 */

namespace Units\{YourDomain}\Manage;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Units\{YourDomain}\Manage\Filament\Initial\Resources\{YourDomain}Resource;

class {YourDomain}ManagePlugin implements Plugin
{
    public static function make()
    {
        return new static();
    }

    public function getId(): string
    {
        return 'filament-{your-domain}-manage-plugin';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                {YourDomain}Resource::class
            ]);
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
```

#### Admin Panel Plugin

```php
<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/28/25, 8:56 AM
 */

namespace Units\{YourDomain}\Admin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Units\{YourDomain}\Admin\Filament\Resources\{YourDomain}Resource;

class {YourDomain}AdminPlugin implements Plugin
{
    public static function make()
    {
        return new static();
    }

    public function getId(): string
    {
        return 'filament-{your-domain}-admin-plugin';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                {YourDomain}Resource::class
            ]);
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
```

### 3. Register Plugins in Panel Configuration

#### Update MyPlugins.php

```php
// In Modules/Units/MyPlugins.php
use Units\{YourDomain}\My\{YourDomain}MyPlugin;

class MyPlugins implements Plugin
{
    public function register(Panel $panel): void
    {
        $panel
            ->plugins([
                AuthMyPlugin::make(),
                ActLogMyPlugin::make(),
                // ... existing plugins
                {YourDomain}MyPlugin::make(),  // Add your plugin
            ]);
    }
}
```

#### Update ManagePlugins.php

```php
// In Modules/Units/ManagePlugins.php
use Units\{YourDomain}\Manage\{YourDomain}ManagePlugin;

class ManagePlugins implements Plugin
{
    public function register(Panel $panel): void
    {
        $panel->plugins([
            AuthManagePlugin::make(),
            ActLogManagePlugin::make(),
            // ... existing plugins
            {YourDomain}ManagePlugin::make(),  // Add your plugin
        ]);
    }
}
```

#### Update AdminPlugins.php

```php
// In Modules/Units/AdminPlugins.php
use Units\{YourDomain}\Admin\{YourDomain}AdminPlugin;

class AdminPlugins implements Plugin
{
    public function register(Panel $panel): void
    {
        $panel->plugins([
            AuthAdminPlugin::make(),
            ActLogAdminPlugin::make(),
            // ... existing plugins
            {YourDomain}AdminPlugin::make(),  // Add your plugin
        ]);
    }
}
```

### 5. Pipeline Integration (Optional)

For complex operations spanning multiple services, consider creating pipeline classes in `Modules/PipLines/Common/`:

```php
<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/28/25, 8:56 AM
 */

namespace FlowServices\Common;

use Modules\Basic\BaseKit\BaseService;

class {YourDomain}Pipeline extends BaseService
{
    public static function send(array $services, array $data): self
    {
        $pipeline = new static();
        return $pipeline->handle($services, $data);
    }
    
    public function handle(array $services, array $data): self
    {
        // Implement cross-service operations
        return $this;
    }
}
```

### 6. Create Shared Components

#### Models

Place your Eloquent models in `Common/Models/`:

```php
<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/28/25, 8:56 AM
 */

namespace Units\{YourDomain}\Common\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class {YourDomain} extends Model
{
    protected $table = '{your_domain_table}';
    
    protected $fillable = [
        'name',
        'description',
        // ... other fillable fields
    ];
    
    // Define relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

#### Enums

Create enum classes in `Common/Enums/`:

```php
<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/28/25, 8:56 AM
 */

namespace Units\{YourDomain}\Common\Enums;

enum {YourDomain}StatusEnum: int
{
    case ACTIVE = 1;
    case INACTIVE = 0;
    case PENDING = 2;
}
```

#### DTOs

Create DTOs in `Common/DTO/`:

```php
<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/28/25, 8:56 AM
 */

namespace Units\{YourDomain}\Common\DTO;

class {YourDomain}DTO
{
    public function __construct(
        public string $name,
        public string $description,
        public int $status
    ) {}
    
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'],
            status: $data['status']
        );
    }
    
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
        ];
    }
}
```

### 7. Create Filament Resources

#### My Panel Resource

```php
<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/28/25, 8:56 AM
 */

namespace Units\{YourDomain}\My\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Units\{YourDomain}\Common\Models\{YourDomain};

class {YourDomain}Resource extends Resource
{
    protected static ?string $model = {YourDomain}::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    public static function getModelLabel(): string
    {
        return 'Your Domain';
    }
    
    public static function getPluralModelLabel(): string
    {
        return 'Your Domains';
    }
    
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('نام')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('توضیحات'),
                // ... other form fields
            ]);
    }
    
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('نام')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('توضیحات'),
                // ... other table columns
            ])
            ->filters([
                // ... filters
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
```

### 8. Create Database Migrations

Place migrations in `Common/database/migrations/`:

```php
<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/28/25, 8:56 AM
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{your_domain_table}', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{your_domain_table}');
    }
};
```

## Best Practices

### 1. Naming Conventions

- Use **CamelCase** for class names
- Use **snake_case** for database tables and columns
- Use **kebab-case** for plugin IDs
- Follow Persian naming for UI labels

### 2. Code Organization

- Keep shared components in the `Common` directory
- Use proper namespacing for each panel
- Follow the established directory structure
- Implement proper separation of concerns

### 3. Plugin Registration

- Always register plugins in the appropriate panel configuration files
- Use descriptive plugin IDs
- Follow the established plugin pattern

### 4. Resource Implementation

- Use Persian labels for all UI elements
- Implement proper form validation
- Follow the established table and form patterns
- Use appropriate icons and colors

## Common Patterns

### Service Integration

For service integration, follow the patterns established in [Service Layer Guidelines](../README.md#service-layer-guidelines).

### Error Handling

Use the established error handling patterns as documented in [HasError Trait](has-error-trait.md).

### SMS Integration

For SMS functionality, follow the patterns in [HasSMS Trait](has-sms-trait.md).

### Notification System

For notifications, use the patterns in [HasNotification Trait](has-notification-trait.md).

## Testing

Create tests following the patterns established in [Testing Guidelines](../README.md#testing-guidelines).

## Related Documentation

- [Filament Panel Registration](filament-panel-registration.md) - Detailed panel registration process
- [Design Patterns](design-patterns.md) - Architectural patterns used in the project
- [DTO Documentation](dto.md) - Data Transfer Object patterns
- [API Documentation](api.md) - API development guidelines
- [Filament Traits Overview](filament-traits-overview.md) - Available traits and their usage

## Example Implementation

For a complete example of panel implementation, refer to the `FinanceRequest` unit structure in `Modules/Units/FinanceRequest/`.

---

**Note**: This guide follows the established patterns in the project. Always refer to existing implementations for specific details and ensure consistency with the current codebase.
