# Entity Workflow Implementation Guide

This documentation provides comprehensive guidance for implementing new features based on the Memorandum Request module, which serves as the most complete implementation in the system.

## Table of Contents

1. [Form Creation and Field Methods](#form-creation-and-field-methods)
2. [Table Creation and Methods](#table-creation-and-methods)
3. [Resource Configuration](#resource-configuration)
4. [Status Management and Panel Handoff](#status-management-and-panel-handoff)
5. [Wizard Implementation and Status Management](#wizard-implementation-and-status-management)
6. [Status Icon and Label Management](#status-icon-and-label-management)
7. [Waiting for Manager Seen Status Management](#waiting-for-manager-seen-status-management)
8. [Form Layout: Two-Column Structure](#form-layout-two-column-structure)
9. [Naming Conventions for Form Folders](#naming-conventions-for-form-folders)
10. [Status Class Organization](#status-class-organization)
11. [Status ENUMs and Management](#status-enums-and-management)
12. [Disabling Main Form Actions and Custom Action Management](#disabling-main-form-actions-and-custom-action-management)

## Form Creation and Field Methods

### Basic Form Schematic Structure

Forms are created using the `BaseFormSchematic` pattern:

```php
<?php
namespace Units\{Module}\{Panel}\Filament\Schematic;

use Modules\Basic\BaseKit\Filament\Schematics\BaseFormSchematic;

class {Module}{Context}FormSchematic extends BaseFormSchematic
{
    public function commonFormSchema(): array
    {
        return [
            // Form components here
        ];
    }

    public function attributeLabels(): array
    {
        return [
            // Field labels in Persian
        ];
    }
}
```

### Available Field Methods

The base schematic provides various helper methods for different field types:

**Basic Components:**
- `textInput('field_name')` - Standard text input
- `selectInput('field_name')` - Select dropdown
- `checkboxInput('field_name')` - Checkbox
- `textAreaInput('field_name')` - Textarea

**Specialized Components:**
- `nationalCodeInput('field_name')` - Iranian national code validation
- `phoneNumberInput('field_name')` - Persian phone number input with validation
- `internationalPhoneNumberInput('field_name')` - International phone format
- `shebaTextInput('field_name')` - IBAN/Sheba validation
- `paymentCardTextInput('field_name')` - Bank card number validation

**Numeric Components:**
- `amountInput('field_name')` - Money input with Rial conversion
- `percentageInput('field_name')` - Percentage input (0-100)
- `month_input('field_name')` - Month counter

**Advanced Components:**
- `repeater('field_name')` - Repeater component with custom actions
- `datePickerInput('field_name')` - Jalali date picker (Iranian calendar)
- `richEditorInput('field_name')` - Rich text editor
- `tinyEditor('field_name')` - TinyMCE editor with RTL support

### Form Schema Structure

Forms follow a two-column layout pattern:

```php
public function commonFormSchema(): array
{
    return [
        Grid::make(12)
            ->schema([
                // Right Side (Main Content)
                Grid::make(1)
                    ->schema([
                        // Main form components
                    ])
                    ->columnSpan(8),

                // Left Side (Sidebar)
                Grid::make(1)
                    ->schema([
                        // Navigation, status, comments, etc.
                    ])
                    ->columnSpan(4),
            ])
    ];
}
```

### Visibility and State Management

Control field visibility and state using configuration methods:

```php
public function invisibleAttributes(): array
{
    return [
        'id' => true,
        'created_at' => true,
        'updated_at' => true
    ];
}

public function disableAttributes(): array
{
    return [
        'id' => 'disabled_save', // Disable but save data
        'created_at' => 'disabled' // Disable and don't save
    ];
}
```

## Table Creation and Methods

### Basic Table Schematic Structure

Tables are created using the `BaseTableSchematic` pattern:

```php
<?php
namespace Units\{Module}\{Panel}\Filament\Schematic;

use Filament\Tables\Table;
use Modules\Basic\BaseKit\Filament\Schematics\BaseTableSchematic;

class {Module}{Context}TableSchema extends BaseTableSchematic
{
    public function tableSchema(Table $table): Table
    {
        return $table
            ->columns([
                // Table columns
            ])
            ->actions([
                // Table actions
            ]);
    }

    public function attributeLabels(): array
    {
        return [
            // Column labels
        ];
    }
}
```

### Available Column Methods

**Basic Columns:**
- `textColumn('field_name')` - Standard text column
- `badgeColumn('field_name')` - Badge with status styling
- `dateTimeColumn('field_name')` - Date/time column
- `moneyColumn('field_name')` - Currency formatting

**Status Columns:**
- `badgeColumn('status_field')` - For status indicators with color and icon

### Table Configuration Methods

```php
public function getActions(): array
{
    return [
        // Define table actions
    ];
}

public function invisibleAttributes(): array
{
    return [
        // Hidden columns
    ];
}

public function disableAttributes(): array
{
    return [
        // Disabled actions
    ];
}
```

### Status Badge Columns

Status columns with dynamic formatting:

```php
$badgeColumn('status_field')
    ->label('عنوان')
    ->formatStateUsing(fn(Model $record) => StatusEnum::getManageLabel($record))
    ->color(fn(Model $record) => StatusEnum::getManageColor($record))
    ->icon(fn(Model $record) => StatusEnum::getManageIcon($record))
    ->visible()
```

## Resource Configuration

### Resource Structure

Resources are configured with separate pages for different operations:

```php
<?php
namespace Units\{Module}\{Panel}\Filament\Resources;

use Filament\Resources\Resource;
use Units\{Module}\{Panel}\Filament\Resources\{Module}Resource\Pages;

class {Module}Resource extends Resource
{
    protected static ?string $model = '{Module}Model::class';
    
    public static function table(Table $table): Table
    {
        return {Module}{Context}TableSchema::makeTable($table)
            ->returnTable();
    }

    public static function form(Form $form): Form
    {
        return {Module}{Context}FormSchematic::makeForm($form)
            ->returnCommonForm();
    }

    protected static function getPages(): array
    {
        return [
            'index' => Pages\List{Module}::route('/'),
            'create' => Pages\Create{Module}::route('/create'),
            'edit' => Pages\Edit{Module}::route('/{record}/edit'),
            'view' => Pages\View{Module}::route('/{record}'),
        ];
    }
}
```

### Page Implementation

Each page extends appropriate base classes:

```php
<?php
namespace Units\{Module}\{Panel}\Filament\Resources\{Module}Resource\Pages;

use Filament\Resources\Pages\EditRecord;
use Units\{Module}\{Panel}\Filament\Schematic\{Module}{Context}FormSchematic;

class Edit{Module} extends EditRecord
{
    protected function getFormActions(): array
    {
        return []; // Disable default actions for custom management
    }

    public function form(Form $form): Form
    {
        return {Module}{Context}FormSchematic::makeForm($form)
            ->setEditResource($this)
            ->returnCommonForm();
    }
}
```

## Status Management and Panel Handoff

### Multi-Panel Status Flow

The system supports status handoff between different panels (My and Manage):

**My Panel (Applicant):**
- Uses `MyPanel{Module}StatusEnum` to manage applicant-side status
- Tracks overall status from applicant perspective

**Manage Panel (Manager):**
- Uses specific status enums for each step
- Manages detailed workflow steps

### Status Enum Structure

Each status has separate methods for different panels:

```php
enum {Status}StatusEnum: string
{
    case WAITING_FOR_MANAGER_SEEN = 'WFMS';
    case WAITING_FOR_MANAGER = 'WFM';
    case APPROVED_BY_MANAGER = 'ABM';
    case REJECTED_BY_MANAGER = 'RBM';
    case RETURNED_BY_MANAGER = 'REBM';

    // My Panel Methods
    public static function getMyStatusLabel(Model $record): string { }
    public static function getMyStatusColor(Model $record): string { }
    public static function getMyStatusIcon(Model $record): string { }

    // Manage Panel Methods
    public static function getManageLabel(Model $record): string { }
    public static function getManageColor(Model $record): string { }
    public static function getManageIcon(Model $record): string { }

    // Business Logic Methods
    public static function canManagerUpdate(Model $record): bool { }
    public static function managerStateIs{Status}(Model $record): bool { }
}
```

### Panel Handoff Logic

Status transitions between panels:

```php
// In Process page
protected function authorizeAccess(): void
{
    parent::authorizeAccess();
    // Check prerequisites
    // Update status to indicate manager has seen the record
    StatusServices::managerSeen($this->record);
}
```

## Wizard Implementation and Status Management

### Wizard Structure

Wizards provide step-by-step navigation through the process:

```php
<?php
namespace Units\{Module}\{Panel}\Filament\Schematic\Right;

use Filament\Forms\Components\Wizard;
use Units\{Module}\Common\Enums\{Module}WizardStepEnum;

class {Module}Wizard
{
    public static function make(): array
    {
        return [
            Wizard::make([
                Wizard\Step::make('پذیرش'),
                Wizard\Step::make('تنظیم'),
                Wizard\Step::make('تایید'),
                Wizard\Step::make('موافقت'),
                Wizard\Step::make('فایل'),
            ])
            ->startOnStep(fn(Model $record) => {Module}WizardStepEnum::getManageCurrentStep($record))
            ->columnSpan(12)
            ->contained(false)
            ->view('components.wizard')
        ];
    }
}
```

### Wizard Step Management

Enum manages current step based on record status:

```php
enum {Module}WizardStepEnum: int
{
    case STEP_1 = 1;
    case STEP_2 = 2;
    case STEP_3 = 3;
    case STEP_4 = 4;
    case STEP_5 = 5;

    public static function getManageCurrentStep(Model $record)
    {
        switch (true) {
            case !empty($record->status1) && empty($record->status2):
                return 1;
            case !empty($record->status1) && !empty($record->status2) && empty($record->status3):
                return 2;
            // ... continue for all steps
        }
    }
}
```

## Status Icon and Label Management

### Icon and Color Standards

Status management follows consistent icon and color patterns:

**Colors:**
- `gray` - New/waiting status
- `success` - Approved/completed
- `danger` - Rejected
- `warning` - In progress
- `info` - Returned for correction

**Icons:**
- `heroicon-s-sparkles` - New items
- `heroicon-s-check-badge` - Approved
- `heroicon-x-circle` - Rejected
- `heroicon-s-exclamation-circle` - Returned
- `heroicon-o-arrow-path` - In progress

### Status Display Methods

Each status enum provides comprehensive display methods:

```php
public static function getManageLabel(Model $record): string { }
public static function getManageColor(Model $record): string { }
public static function getManageIcon(Model $record): string { }
```

## Waiting for Manager Seen Status Management

### Automatic Status Change

When a manager views a record with "waiting for seen" status, it automatically changes:

```php
// In Process page mount method
public function mount(int|string $record): void
{
    parent::mount($record);
    $this->record->addManagerComment('مشاهده اطلاعات توسط کارشناس', 'seen');
}

// In authorizeAccess or beforeFill
protected function authorizeAccess(): void
{
    parent::authorizeAccess();
    StatusServices::managerSeen($this->record);
}

// In Status Enum
public static function managerSeen(Model $record): void
{
    if ($record->status_field == self::WAITING_FOR_MANAGER_SEEN->value) {
        $record->update(['status_field' => self::WAITING_FOR_MANAGER->value]);
    }
}
```

### Status Change Triggers

Status changes occur in multiple places:
- `mount()` method when page loads
- `authorizeAccess()` for permission checks
- `beforeFill()` for form initialization

## Form Layout: Two-Column Structure

### Column Organization

Forms use a two-column layout with specific responsibilities:

**Right Column (8 spans):**
- Main form content
- Wizard steps
- Primary data entry fields
- Action buttons

**Left Column (4 spans):**
- Navigation sidebar
- Current status display
- Comments and chat
- Action history
- Related information

### Grid Structure

```php
Grid::make(12)
    ->schema([
        // Right Side
        Grid::make(1)
            ->schema([
                // Main content components
            ])
            ->columnSpan(8),

        // Left Side
        Grid::make(1)
            ->schema([
                // Sidebar components
            ])
            ->columnSpan(4),
    ])
```

### Component Organization

Components are organized in separate schematic files:
- `Left/` folder for sidebar components
- `Right/` folder for main content components
- Combined in main form schematic

## Naming Conventions for Form Folders

### Directory Structure

```
Modules/Units/{Module}/
├── Common/
│   ├── Models/
│   ├── Enums/
│   ├── Statuses/
│   └── Services/
├── {Panel}/
│   ├── Filament/
│   │   ├── Resources/
│   │   └── Schematic/
│   │       ├── {Module}{Context}FormSchematic.php
│   │       ├── {Module}{Context}TableSchema.php
│   │       ├── Left/
│   │       │   └── {Component}Schema.php
│   │       └── Right/
│   │           └── {Component}Schema.php
```

### File Naming Patterns

- **Form Schematics:** `{Module}{Context}FormSchematic.php`
- **Table Schematics:** `{Module}{Context}TableSchema.php`
- **Left Components:** `{Component}Schema.php`
- **Right Components:** `{Component}Schema.php`
- **Status Enums:** `{Status}StatusEnum.php`
- **Status Services:** `{Status}ActionService.php`
- **Status Schematics:** `{Status}FormActionSchema.php`

## Status Class Organization

### Status Directory Structure

```
Common/Statuses/{StatusName}/
├── Enums/
│   └── {Status}StatusEnum.php
├── Services/
│   └── {Status}ActionService.php
├── Schematics/
│   └── {Status}FormActionSchema.php
```

### Status Class Responsibilities

**Enum Class:**
- Defines status cases
- Provides display methods for different panels
- Contains business logic methods

**Service Class:**
- Implements status change logic
- Handles related operations
- Manages side effects

**Schematic Class:**
- Defines form actions for the status
- Manages action visibility and behavior
- Handles action-specific logic

## Status ENUMs and Management

### ENUM Structure

Status ENUMs follow a comprehensive pattern:

```php
enum {Status}StatusEnum: string
{
    case CASE_1 = 'CODE1';
    case CASE_2 = 'CODE2';
    case CASE_3 = 'CODE3';

    // My Panel Display Methods
    public static function getMyStatusLabel(Model $record): string { }
    public static function getMyStatusColor(Model $record): string { }
    public static function getMyStatusIcon(Model $record): string { }

    // Manage Panel Display Methods
    public static function getManageLabel(Model $record): string { }
    public static function getManageColor(Model $record): string { }
    public static function getManageIcon(Model $record): string { }

    // Business Logic Methods
    public static function canManagerUpdate(Model $record): bool { }
    public static function is{Status}(Model $record): bool { }
    public static function managerSeen(Model $record): void { }
}
```

### Status Management Best Practices

- Use consistent case names and codes
- Provide display methods for both panels
- Include business logic validation
- Handle status transitions properly
- Maintain backward compatibility

## Disabling Main Form Actions and Custom Action Management

### Disabling Default Actions

Disable default form actions to implement custom management:

```php
class {Module}Page extends EditRecord
{
    protected function getFormActions(): array
    {
        return []; // Disable default save/cancel buttons
    }

    protected function getHeaderActions(): array
    {
        return []; // Disable header actions if needed
    }
}
```

### Custom Action Implementation

Custom actions are implemented through schematic classes:

```php
<?php
namespace Units\{Module}\Common\Statuses\{Status}\Schematics;

use Filament\Forms\Components\Actions\Action;
use Modules\Basic\BaseKit\Filament\Schematics\BaseFormSchematic;

class {Status}FormActionSchema extends BaseFormSchematic
{
    public function commonFormSchema(): array
    {
        return [
            $this->approveAction(),
            $this->rejectAction(),
            $this->returnToApplicantAction(),
            $this->saveAction(),
        ];
    }

    public function approveAction(): Action
    {
        return $this->approveAction('approve_{status}')
            ->visible(fn(Model $record) => {Status}StatusEnum::canManagerUpdate($record))
            ->action(fn(Model $record) => {Status}ActionService::Approve($record, $this->getEditResource()))
            ->extraAttributes(['class' => 'h-20 float-left', 'style' => 'width:47%']);
    }

    public function rejectAction(): Action
    {
        return $this->rejectAction('reject_{status}')
            ->color('danger')
            ->visible(fn(Model $record) => {Status}StatusEnum::canManagerUpdate($record))
            ->action(fn(Model $record, $data) => {Status}ActionService::Reject($record, $this->getEditResource(), $data['reject_reason']))
            ->extraAttributes(['class' => 'h-20 float-left', 'style' => 'width:47%']);
    }
}
```

### Action Service Pattern

Action services handle the business logic:

```php
<?php
namespace Units\{Module}\Common\Statuses\{Status}\Services;

class {Status}ActionService
{
    public static function Approve(Model $record, Page $page): void
    {
        DB::beginTransaction();
        try {
            // Update status
            $record->update(['status_field' => NextStatusEnum::APPROVED->value]);
            
            // Add comments
            $record->addManagerComment('تایید شده', 'approve');
            
            // Send notifications
            $page->alert_success('عملیات با موفقیت انجام شد');
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $page->alert_error('خطا در انجام عملیات');
        }
    }
}
```

### Status-Based Action Visibility

Actions are visible based on current status and permissions:

```php
->visible(
    fn(Model $record) =>
        {Status}StatusEnum::managerStateIs{Status}($record) &&
        {Status}StatusEnum::canManagerUpdate($record)
```

This implementation guide provides the complete pattern for building new features based on the Memorandum Request module, ensuring consistency and maintainability across the system.
