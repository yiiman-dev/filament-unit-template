# Panel Creation Guide

This guide explains how to create new panels in the Filament project following the established architecture patterns.

## Overview

Panels in this project follow a modular structure where each unit can have multiple panels (My, Manage, Admin) with shared components in a Common directory. This architecture ensures code reusability and maintains separation of concerns.

## Directory Structure

```
Modules/Units/{UnitName}/
├── Common/                   # Shared components across all panels
│   ├── Models/               # Eloquent models 
│   ├── Enums/                # Enum classes (if needed)
│   ├── DTO/                  # Data Transfer Objects (if needed)
│   ├── Repository/           # Repository classes (if needed)
│   ├── Filament/             # Shared Filament components (if needed)
│   │   ├── Tables/           # Table schemas (if needed)
│   │   ├── Forms/            # Form schemas (if needed)
│   │   └── Resources/        # Shared resources (if needed)
│   └── database/             # Migrations and seeders and factories
│       ├── migrations/       # migration files
│       ├── factories/        # Model factories (generate based on migrations and relations)
│       └── seeders/          # Model Seeders
├── My/                       # My panel (user-facing)
│   ├── {UnitName}MyPlugin.php
│   └── Filament/
│       ├── Resources/        # My panel resources
│       └── Schematic/        # My panel schematics
├── Manage/                   # Manage panel (management interface)
│   ├── {UnitName}ManagePlugin.php
│   └── Filament/
│       │   ├── Resources/
│       │   └── Schematic/
│       └── Advanced/         # Advanced management resources
└── Admin/                    # Admin panel (administrative interface)
    ├── {UnitName}AdminPlugin.php
    └── Filament/
        ├── Resources/
        └── Schematic/

```

## Step-by-Step Panel Creation

### 1. Create Unit Directory Structure

First, create the main unit directory under `Modules/Units/`. The unit name should be changed to represent your specific unit (e.g., Product, Order, User):

```bash
mkdir -p Modules/Units/{UnitName}/Common/{Models,Enums,DTO,Repository,Filament/{Tables,Forms,Resources},database/{migrations,factories,seeders}}
mkdir -p Modules/Units/{UnitName}/My/Filament/{Resources,Schematic}
mkdir -p Modules/Units/{UnitName}/Manage/Filament/{Resources,Schematic}
mkdir -p Modules/Units/{UnitName}/Admin/Filament/{Resources,Schematic}
```

### 2. Create Plugin Classes

Each panel requires its own plugin class to manage the resources and functionality specific to that panel type. The plugin class defines what resources are available in each panel.

#### My Panel Plugin

The My panel is user-facing and contains resources that end users interact with:

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

namespace Units\{UnitName}\My;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Units\{UnitName}\My\Filament\Resources\{UnitName}Resource;

class {UnitName}MyPlugin implements Plugin
{
    public static function make()
    {
        return new static();
    }

    public function getId(): string
    {
        return 'filament-{your-unit}-my-plugin';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                {UnitName}Resource::class
            ]);
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
```

#### Manage Panel Plugin (Detailed Example)

The Manage panel is for management interfaces. For example, if you want to create a product plugin available on the manage panel, you would create a `Manage` directory under your unit and create the plugin class there:

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

namespace Units\{UnitName}\Manage;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Units\{UnitName}\Manage\Filament\Resources\{UnitName}Resource;

class {UnitName}ManagePlugin implements Plugin
{
    public static function make()
    {
        return new static();
    }

    public function getId(): string
    {
        return 'filament-{your-unit}-manage-plugin';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                {UnitName}Resource::class
                // Add more resources as needed for management panel
            ]);
    }

    public function boot(Panel $panel): void
    {
        // Management-specific initialization
    }
}
```

**Real Example**: Following the pattern of the Shield unit, you can look at `Modules/Units/Shield` to see how plugins are structured and registered.

#### Admin Panel Plugin

The Admin panel is for administrative functions:

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

namespace Units\{UnitName}\Admin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Units\{UnitName}\Admin\Filament\Resources\{UnitName}Resource;

class {UnitName}AdminPlugin implements Plugin
{
    public static function make()
    {
        return new static();
    }

    public function getId(): string
    {
        return 'filament-{unit-name}-admin-plugin';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                {UnitName}Resource::class
            ]);
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
```

### 3. Create Resources for Each Panel

After creating the plugin class, you need to create the actual Filament resources in the appropriate directories:

#### Create Resources in Manage Directory

For the Manage panel, create your resources in `Modules/Units/{UnitName}/Manage/Filament/Resources/`:

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

namespace Units\{UnitName}\Manage\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Units\{UnitName}\Common\Models\{UnitName};
use Units\{UnitName}\Manage\Filament\Schematic\{UnitName}FormSchema;
use Units\{UnitName}\Manage\Filament\Schematic\{UnitName}TableSchema;
/**
 * @property {UnitModelClass} $record 
 */
class {UnitName}Resource extends Resource
{
    protected static ?string $model = {UnitModel}::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    public static function canAccess(): bool
    {
        // Optional: Add access control logic here
        return parent::canAccess();
    }
    
    public static function getModelLabel(): string
    {
        return 'Your Unit Name in persian';
    }
    
    public static function getPluralModelLabel(): string
    {
        return 'Your Unit Name in persian';
    }
    
    public static function form(Forms\Form $form): Forms\Form
    {
        return {UnitName}FormSchema::makeForm($form)
            ->returnCommonForm();
    }
    
    public static function table(Tables\Table $table): Tables\Table
    {
        return {UnitName}TableSchema::makeTable($table)
            ->returnTable();
    }
    
    public static function getPages(): array
    {
        return [
            'index' => \Units\{UnitName}\Manage\Filament\Resources\Pages\List{UnitName}s::route('/'),
            'create' => \Units\{UnitName}\Manage\Filament\Resources\Pages\Create{UnitName}s::route('/create'),
            'edit' => \Units\{UnitName}\Manage\Filament\Resources\Pages\Edit{UnitName}s::route('/{record}/edit'),
            'view' => \Units\{UnitName}\Manage\Filament\Resources\Pages\View{UnitName}s::route('/{record}/view'),
        ];
    }
}
```

### 4. Register Plugins in Panel Configuration

After creating your plugin class and resources, you must register the plugin in the corresponding panel configuration file:

#### Register in ManagePlugins.php

Add your plugin to `Modules/Units/ManagePlugins.php`:

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

namespace Modules\Units;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Units\Auth\Manage\AuthManagePlugin;
use Units\ActivityLog\Manage\ActLogManagePlugin;
// Import your new plugin
use Units\{UnitName}\Manage\{UnitName}ManagePlugin;

class ManagePlugins implements Plugin
{
    public function register(Panel $panel): void
    {
        $panel->plugins([
            AuthManagePlugin::make(),
            ActLogManagePlugin::make(),
            // ... existing plugins
            {UnitName}ManagePlugin::make(),  // Add your plugin here
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
```

#### Register in MyPlugins.php

Similarly, for the My panel, update `Modules/Units/MyPlugins.php`:

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

namespace Modules\Units;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Units\Auth\My\AuthMyPlugin;
use Units\ActivityLog\My\ActLogMyPlugin;
// Import your new plugin
use Units\{UnitName}\My\{UnitName}MyPlugin;

class MyPlugins implements Plugin
{
    public function register(Panel $panel): void
    {
        $panel
            ->plugins([
                AuthMyPlugin::make(),
                ActLogMyPlugin::make(),
                // ... existing plugins
                {UnitName}MyPlugin::make(),  // Add your plugin here
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
```

#### Register in AdminPlugins.php

For the Admin panel, update `Modules/Units/AdminPlugins.php`:

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

namespace Modules\Units;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Units\Auth\Admin\AuthAdminPlugin;
use Units\ActivityLog\Admin\ActLogAdminPlugin;
// Import your new plugin
use Units\{UnitName}\Admin\{UnitName}AdminPlugin;

class AdminPlugins implements Plugin
{
    public function register(Panel $panel): void
    {
        $panel->plugins([
            AuthAdminPlugin::make(),
            ActLogAdminPlugin::make(),
            // ... existing plugins
            {UnitName}AdminPlugin::make(),  // Add your plugin here
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
```

### 6. Create Shared Components

#### Models

Place your Eloquent models in `Common/Models/`. Every model that has status fields must follow specific laws:

**Model Creation Laws:**
1. Every model that has fields like `status` should have scope functions for every status
2. Status values must be defined using Enum classes only
3. Every model must have a docblock comment on top of the class that includes:
   - All available scopes
   - All database properties (fillable, casts, etc.)
   - Persian comments for every field in the database table

**Example Model with Proper Documentation:**

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

namespace Units\{UnitName}\Common\Models;

use Units\ActLog\Common\Observers\CommonChangeModelLogObserver;
use Modules\Basic\BaseKit\Observers\BaseObserver;
use Modules\Basic\BaseKit\Model\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Units\{UnitName}\Common\Enums\{UnitName}StatusEnum;

/**
 * Class {UnitName}
 * 
 * Scopes:
 * @method static \Illuminate\Database\Eloquent\Builder|\Units\{UnitName}\Common\Models\{UnitName} active()
 * @method static \Illuminate\Database\Eloquent\Builder|\Units\{UnitName}\Common\Models\{UnitName} inactive()
 * @method static \Illuminate\Database\Eloquent\Builder|\Units\{UnitName}\Common\Models\{UnitName} pending()
 * 
 * Database Properties:
 * @property int $id
 * @property string $name نام (نام واحد)
 * @property string $description توضیحات (توضیحات مربوط به واحد)
 * @property int $status وضعیت (وضعیت فعال/غیرفعال واحد - از enum {UnitName}StatusEnum)
 * @property int $user_id کد کاربر (کد کاربر مرتبط با واحد)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * Fillable Attributes:
 * - name: نام واحد
 * - description: توضیحات واحد
 * - status: وضعیت واحد (استفاده از {UnitName}StatusEnum)
 * - user_id: کد کاربر مرتبط
 */
class {UnitName} extends BaseModel
{
    protected $table = '{your_unit_table}';
    public $incrementing = false;// Use if primary key is not integer or is string or uuid
    protected $keyType = 'string';// Use if primary key is not integer or is string or uuid
    protected static function booted()
    {
        //This is default and required observers that should added to all models
        static::observe(CommonChangeModelLogObserver::class);
        static::observe(BaseObserver::class);
        parent::booted();
    }
    
    
    protected $fillable = [
        'id',             // if we have id field on migration, then we should add it to fillable because observers should known this field and act on it
        'name',           // نام واحد
        'description',    // توضیحات واحد
        'status',         // وضعیت واحد (از enum استفاده شود)
        'user_id',        // کد کاربر مرتبط
    ];
    
    protected $casts = [
        'id'=>'string', // if we have id field on migration and type that is not integer, then we should add it to casts because observers should known this field and act on it
        'status' => {UnitName}StatusEnum::class,  // استفاده از enum برای وضعیت
    ];
    
    // Define relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
    
    // Scope functions for each status
    public function scopeActive($query)
    {
        return $query->where('status', {UnitName}StatusEnum::ACTIVE->value);
    }
    
    public function scopeInactive($query)
    {
        return $query->where('status', {UnitName}StatusEnum::INACTIVE->value);
    }
    
    public function scopePending($query)
    {
        return $query->where('status', {UnitName}StatusEnum::PENDING->value);
    }
    
    public function original_connection(): string
    {
        //This method is required, usually we use manage connection
        return 'manage';
    }
}
```

**Enum Class for Status (Required):**

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

namespace Units\{UnitName}\Common\Enums;

enum {UnitName}StatusEnum: int
{
    case ACTIVE = 1;    // فعال
    case INACTIVE = 0;  // غیرفعال
    case PENDING = 2;   // در حال بررسی
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

namespace Units\{UnitName}\Common\Enums;

enum {UnitName}StatusEnum: int
{
    case ACTIVE = 1;
    case INACTIVE = 0;
    case PENDING = 2;
    
    
    public static function getLabels(): array
    {
        return [
            self::ACTIVE->value => 'فعال',
            self::INACTIVE->value => 'غیرفعال',
            self::PENDING->value => 'در انتظار',
        ];
    }
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

namespace Units\{UnitName}\Common\DTO;

class {UnitName}DTO
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

### 7. Create Schematic Classes

Every resource for defining table, info list, and forms should create and use Schematic classes. Before creating any schematic class, please read the [Schematic Pattern Documentation](./schematic-pattern.md) file to understand the pattern and best practices.

**Schematic Classes Requirements:**
1. All table schemas must be implemented using schematic classes extending `BaseTableSchematic`
2. All form schemas must be implemented using schematic classes extending `BaseFormSchematic`
3. All info list schemas must be implemented using schematic classes extending `BaseViewSchematic`
4. Each panel (My, Manage, Admin) should have its own schematic implementations
5. Use the specialized field methods provided by the base classes (e.g., `$this->textInput()`, `$this->textColumn()`, etc.)

**Example Schematic Classes:**

**Table Schema:**
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

namespace Units\{UnitName}\Manage\Filament\Schematic;

use Modules\Basic\BaseKit\Filament\Schematics\BaseTableSchematic;
use Filament\Tables\Table;

class {UnitName}TableSchema extends BaseTableSchematic
{
    function tableSchema(Table $table): Table
    {
        return $table
            ->columns([
                $this->textColumn('name')
                ->visible(),
                $this->textColumn('description')
                ->visible(),
                $this->badgeColumn('status')
                ->visible(),
                // ... other columns using schematic methods
            ])
            ->filters([
                // Add filters here
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public function attributeLabels(): array
    {
        return [
            'name' => 'نام',
            'description' => 'توضیحات',
            'status' => 'وضعیت',
        ];
    }
}
```

**Form Schema:**
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

namespace Units\{UnitName}\Manage\Filament\Schematic;

use Modules\Basic\BaseKit\Filament\Schematics\BaseFormSchematic;
use Filament\Forms\Form;

class {UnitName}FormSchema extends BaseFormSchematic
{
    function commonFormSchema(): array
    {
        return [
            $this->textInput('name')
            ->visible(),
            $this->tinyEditor('description')
            ->visible(),
            $this->statusSelectField('status',{UnitName}StatusEnum::getLabels())
            ->visible(),
            // ... other fields using schematic methods
        ];
    }

    
    public function attributeLabels(): array
    {
        return [
            'name' => 'نام',
            'description' => 'توضیحات',
            'status' => 'وضعیت',
        ];
    }
}
```


### 9. Create Database Migrations

Place migrations in `Common/database/migrations/`:
(If You are AI Agent, You should not build any migration if exists any migration files)
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
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('manage'))->create('{your_unit_table}', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection(\Modules\Basic\Helpers\Helper::migrationConnection('manage'))->dropIfExists('{your_unit_table}');
    }
};
```

## Creating Pages and Actions for Better UX

### Page Structure and Types

Filament resources come with four standard page types that should be created for each resource:

#### 1. List Page (Index)

The list page shows all records in a table format. Create in `Modules/Units/{UnitName}/Manage/Filament/Resources/Pages/List{UnitName}s.php`:

```php
<?php

namespace Units\{UnitName}\Manage\Filament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Units\{UnitName}\Manage\Filament\Resources\{UnitName}Resource;

class List{UnitName}s extends ListRecords
{
    protected static string $resource = {UnitName}Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
```

#### 2. Create Page

The create page handles new record creation. Create in `Modules/Units/{UnitName}/Manage/Filament/Resources/Pages/Create{UnitName}s.php`:

```php
<?php

namespace Units\{UnitName}\Manage\Filament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Units\{UnitName}\Manage\Filament\Resources\{UnitName}Resource;

class Create{UnitName}s extends CreateRecord
{
    protected static string $resource = {UnitName}Resource::class;
}
```

#### 3. Edit Page

The edit page allows modification of existing records. Create in `Modules/Units/{UnitName}/Manage/Filament/Resources/Pages/Edit{UnitName}s.php`:

```php
<?php

namespace Units\{UnitName}\Manage\Filament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Units\{UnitName}\Manage\Filament\Resources\{UnitName}Resource;

class Edit{UnitName}s extends EditRecord
{
    protected static string $resource = {UnitName}Resource::class;
}
```

#### 4. View Page (Show Page)

The view page displays record details with enhanced actions. Create in `Modules/Units/{UnitName}/Manage/Filament/Resources/Pages/View{UnitName}s.php`:

```php
<?php

namespace Units\{UnitName}\Manage\Filament\Resources\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Units\{UnitName}\Common\Enums\{UnitName}StatusEnum;
use Units\{UnitName}\Common\Models\{UnitName};
use Units\{UnitName}\Manage\Filament\Resources\{UnitName}Resource;

/**
 * @property {UnitName} $record
 */
class View{UnitName}s extends ViewRecord
{
    protected static string $resource = {UnitName}Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->record($this->record),
            Action::make('toggle_status')
                ->label($this->record->status->value === {UnitName}StatusEnum::ACTIVE->value ? 'غیرفعال کردن' : 'فعال کردن')
                ->color($this->record->status->value === {UnitName}StatusEnum::ACTIVE->value ? 'danger' : 'success')
                ->action(function () {
                    $this->record->update([
                        'status' => $this->record->status->value === {UnitName}StatusEnum::ACTIVE->value
                            ? {UnitName}StatusEnum::INACTIVE->value
                            : {UnitName}StatusEnum::ACTIVE->value
                    ]);
                    $this->redirect({UnitName}Resource::getUrl('view', ['record' => $this->record]));
                })
                ->icon('heroicon-o-power'),
            DeleteAction::make()
                ->record($this->record),
        ];
    }
}
```

#### Real Example: PortalDataLog Module

Here's a real example from the `PortalDataLog` module showing how the pages and actions are implemented:

**PortalDataLog List Page:**
```php
<?php

namespace Units\Synchronization\PortalDataLog\Manage\Filament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Units\Synchronization\PortalDataLog\Manage\Filament\Resources\PortalDataLogResource;

class ListPortalDataLogs extends ListRecords
{
    protected static string $resource = PortalDataLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
```

**PortalDataLog Create Page:**
```php
<?php

namespace Units\Synchronization\PortalDataLog\Manage\Filament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Units\Synchronization\PortalDataLog\Manage\Filament\Resources\PortalDataLogResource;

class CreatePortalDataLogs extends CreateRecord
{
    protected static string $resource = PortalDataLogResource::class;
}
```

**PortalDataLog Edit Page:**
```php
<?php

namespace Units\Synchronization\PortalDataLog\Manage\Filament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Units\Synchronization\PortalDataLog\Manage\Filament\Resources\PortalDataLogResource;

class EditPortalDataLogs extends EditRecord
{
    protected static string $resource = PortalDataLogResource::class;
}
```

**PortalDataLog View Page with Enhanced Actions:**
```php
<?php

namespace Units\Synchronization\PortalDataLog\Manage\Filament\Resources\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Units\Synchronization\PortalDataLog\Common\Models\PortalDataLog;
use Units\Synchronization\PortalDataLog\Manage\Filament\Resources\PortalDataLogResource;

/**
 * @property PortalDataLog $record
 */
class ViewPortalDataLogs extends ViewRecord
{
    protected static string $resource = PortalDataLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->record($this->record),
            Action::make('refresh_log')
                ->label('بازخوانی لاگ')
                ->color('primary')
                ->action(function () {
                    // Refresh log functionality
                    $this->record->refresh();
                    $this->refresh();
                })
                ->icon('heroicon-o-arrow-path'),
            DeleteAction::make()
                ->record($this->record),
        ];
    }
}
```

**PortalDataLog Resource with Pages Configuration:**
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

namespace Units\Synchronization\PortalDataLog\Manage\Filament\Resources;

use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Units\Synchronization\PortalDataLog\Common\Models\PortalDataLog;
use Units\Synchronization\PortalDataLog\Manage\Filament\Schematic\PortalDataLogFormSchema;
use Units\Synchronization\PortalDataLog\Manage\Filament\Schematic\PortalDataLogTableSchema;

/**
 * @property PortalDataLog $record
 */
class PortalDataLogResource extends Resource
{
    protected static ?string $model = PortalDataLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function getModelLabel(): string
    {
        return 'ثبت داده پورتال';
    }

    public static function getPluralModelLabel(): string
    {
        return 'ثبت‌های داده پورتال';
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return PortalDataLogFormSchema::makeForm($form)
            ->returnCommonForm();
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return PortalDataLogTableSchema::makeTable($table)
            ->returnTable();
    }

    public static function getPages(): array
    {
        return [
            'index' => \Units\Synchronization\PortalDataLog\Manage\Filament\Resources\Pages\ListPortalDataLogs::route('/'),
            'create' => \Units\Synchronization\PortalDataLog\Manage\Filament\Resources\Pages\CreatePortalDataLogs::route('/create'),
            'edit' => \Units\Synchronization\PortalDataLog\Manage\Filament\Resources\Pages\EditPortalDataLogs::route('/{record}/edit'),
            'view' => \Units\Synchronization\PortalDataLog\Manage\Filament\Resources\Pages\ViewPortalDataLogs::route('/{record}/view'),
        ];
    }
}
```

### Essential Actions for Better UX

#### 1. Standard Table Actions

Include these actions in your table schema for better user experience:

```php
->actions([
    \Filament\Tables\Actions\EditAction::make(),
    \Filament\Tables\Actions\ViewAction::make(),
    \Filament\Tables\Actions\DeleteAction::make(), // Optional based on business logic
    // Custom actions like status toggle
    \Filament\Tables\Actions\Action::make('toggle_status')
        ->label('تغییر وضعیت')
        ->color(fn($record) => $record->status === StatusEnum::ACTIVE->value ? 'danger' : 'success')
        ->action(function ($record) {
            $record->update([
                'status' => $record->status === StatusEnum::ACTIVE->value
                    ? StatusEnum::INACTIVE->value
                    : StatusEnum::ACTIVE->value
            ]);
        })
        ->icon('heroicon-o-power')
        ->requiresConfirmation(),
])
```

#### 2. Header Actions for List Page

Add useful header actions to the list page:

```php
protected function getHeaderActions(): array
{
    return [
        \Filament\Actions\CreateAction::make(),
        // Optional: Bulk actions
        \Filament\Actions\Action::make('export')
            ->label('خروجی اکسل')
            ->icon('heroicon-o-arrow-down-tray')
            ->action(function () {
                // Export logic here
            }),
    ];
}
```

#### 3. Header Actions for View Page

Enhance the view page with comprehensive actions:

```php
protected function getHeaderActions(): array
{
    return [
        EditAction::make()
            ->record($this->record),
        Action::make('duplicate')
            ->label('ایجاد کپی')
            ->icon('heroicon-o-document-duplicate')
            ->action(function () {
                // Duplicate logic
            })
            ->visible(fn($record) => $record->canDuplicate()),
        Action::make('toggle_status')
            ->label(fn($record) => $record->status->value === StatusEnum::ACTIVE->value ? 'غیرفعال کردن' : 'فعال کردن')
            ->color(fn($record) => $record->status->value === StatusEnum::ACTIVE->value ? 'danger' : 'success')
            ->action(function () {
                $this->record->update([
                    'status' => $this->record->status->value === StatusEnum::ACTIVE->value
                        ? StatusEnum::INACTIVE->value
                        : StatusEnum::ACTIVE->value
                ]);
                $this->refresh();
            })
            ->icon('heroicon-o-power'),
        DeleteAction::make()
            ->record($this->record)
            ->visible(fn($record) => $record->canBeDeleted()),
    ];
}
```

#### 4. Contextual Actions Based on Record State

Create actions that adapt based on the record's current state:

```php
// In View page actions
Action::make('process')
    ->label('پردازش')
    ->color('warning')
    ->action(function () {
        // Process logic
    })
    ->visible(fn($record) => $record->status === StatusEnum::PENDING)
    ->requiresConfirmation(),

Action::make('cancel')
    ->label('لغو')
    ->color('danger')
    ->action(function () {
        // Cancel logic
    })
    ->visible(fn($record) => $record->status === StatusEnum::ACTIVE),
```

### Best Practices for Actions

#### 1. Action Naming and Icons

- Use clear, Persian labels that describe the action
- Choose appropriate heroicons for visual recognition
- Maintain consistency across similar actions

#### 2. Confirmation Dialogs

Always use confirmation dialogs for destructive actions:

```php
->requiresConfirmation()
->modalHeading('آیا مطمئن هستید؟')
->modalDescription('این عملیات قابل بازگشت نیست.')
->modalSubmitActionLabel('بله، حذف کن'),
```

#### 3. Action Colors

Use semantic colors appropriately:
- `success`: For positive actions (activate, approve)
- `danger`: For destructive actions (delete, deactivate)
- `warning`: For cautionary actions (process, modify)
- `primary`: For standard actions (edit, view)

#### 4. Conditional Visibility

Make actions contextually relevant:

```php
->visible(fn($record) => auth()->user()->can('edit', $record))
->enabled(fn($record) => $record->isEditable())
```

### Best Practices

#### 1. Naming Conventions

- Use **CamelCase** for class names
- Use **snake_case** for database tables and columns
- Use **kebab-case** for plugin IDs
- Follow Persian naming for UI labels

#### 2. Code Organization

- Keep shared components in the `Common` directory
- Use proper namespacing for each panel
- Follow the established directory structure
- Implement proper separation of concerns

#### 3. Plugin Registration

- Always register plugins in the appropriate panel configuration files
- Use descriptive plugin IDs
- Follow the established plugin pattern

#### 4. Resource Implementation

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
