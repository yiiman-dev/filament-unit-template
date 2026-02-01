# Filament Schematic Pattern Documentation

## Overview

The Filament Schematic pattern is a structured approach to building Filament forms, tables, and info lists in Laravel applications. It provides a standardized way to organize form components, manage visibility, disable states, and handle common UI patterns.

## Core Components

### Base Schematic Classes

1. **BaseFormSchematic** - For form schemas (create, edit, common)
2. **BaseTableSchematic** - For table schemas
3. **BaseViewSchematic** - For info list schemas

### Directory Structure

```
Modules/Basic/app/BaseKit/Filament/Schematics/
├── BaseFormSchematic.php
├── BaseTableSchematic.php
├── BaseViewSchematic.php
├── Schematic.php
└── Concerns/
    └── InteractWithForm.php
```

## Architecture

### Schematic Base Classes

Each schematic class extends `Schematic` which provides:
- Attribute management (visible, disabled)
- Label handling
- Configuration methods

### Form Schematic Usage Flow

1. **Create Schematic Class**: Extend `BaseFormSchematic`
2. **Implement Required Methods**:
   - `commonFormSchema()` - Main form schema
   - `editFormSchema()` - Edit-specific schema (optional)
   - `createFormSchema()` - Create-specific schema (optional)
   - `attributeLabels()` - Field labels
   - `invisibleAttributes()` - Hidden fields
   - `disableAttributes()` - Disabled fields

3. **Usage in Pages**:
```php
public function form(Form $form): Form
{
    return NaturalRegisterFormSchematic::makeForm($form)
        ->returnCommonForm();
}
```

## Key Features

### 1. Component Management

All form components are managed through helper methods that provide:
- Automatic labeling
- Hint and placeholder support
- Visibility control
- Disable state management

### 2. Component Helper Methods

```php
// Basic components
$this->textInput('field_name')
$this->selectInput('field_name')
$this->checkboxInput('field_name')

// Specialized components
$this->nationalCodeInput('field_name')
$this->phoneNumberInput('field_name')
$this->shebaTextInput('field_name')
$this->paymentCardTextInput('field_name')

// Advanced components
$this->amountInput('field_name')
$this->percentageInput('field_name')
$this->repeater('field_name')
```

### 3. Visibility and State Management

```php
// Make field visible/invisible
$this->visibleAttribute('field_name')
$this->invisibleAttribute('field_name')

// Make field disabled/enabled
$this->disableAttribute('field_name')
$this->enableAttribute('field_name')

// Make field disabled but still save data
$this->disableAttribute('field_name', true) // save=true
```

### 4. Configuration Support

Each component automatically applies:
- Labels (from `attributeLabels()`)
- Hints (from `attributeHints()`)
- Placeholders (from `attributePlaceholders()`)
- Helper texts (from `attributeHelperTexts()`)
- Default values (from `attributeDefaults()`)
- Disabled states (from `disableAttributes()`)

## Implementation Pattern

### 1. Basic Form Schematic

```php
class MyFormSchematic extends BaseFormSchematic
{
    public function attributeLabels(): array
    {
        return [
            'name' => 'نام',
            'email' => 'ایمیل'
        ];
    }

    public function commonFormSchema(): array
    {
        return [
            Section::make('اطلاعات اصلی')
                ->schema([
                    $this->textInput('name')
                        ->required(),
                    $this->emailInput('email')
                        ->required()
                ])
        ];
    }

    public function invisibleAttributes(): array
    {
        return [
            'id' => true // Hide the id field
        ];
    }

    public function disableAttributes(): array
    {
        return [
            'created_at' => 'disabled', // Disable but not save
            'updated_at' => 'disabled_save' // Disable and save data
        ];
    }
}
```

### 2. Usage in Filament Pages

```php
class MyPage extends BasePage implements HasForms
{
    public function form(Form $form): Form
    {
        return MyFormSchematic::makeForm($form, 'common')
            ->returnCommonForm();
    }
}
```

## Schematic Method Patterns

### Form Schema Methods

1. **`commonFormSchema()`** - Main form structure (used for create/edit)
2. **`editFormSchema()`** - Edit-specific overrides (optional)
3. **`createFormSchema()`** - Create-specific overrides (optional)

### Configuration Methods

1. **`attributeLabels()`** - Field labels for Persian UI
2. **`attributeHints()`** - Hint text for fields (optional)
3. **`attributePlaceholders()`** - Placeholder text (optional)
4. **`attributeDefaults()`** - Default values (optional)
5. **`attributeHelperTexts()`** - Helper text for fields (optional)

### Visibility Methods

1. **`invisibleAttributes()`** - Fields to hide entirely
2. **`disableAttributes()`** - Fields to disable (with optional save)

## Advanced Features

### 1. Remote Schema Inheritance

```php
// Inherit labels and configurations from another schematic
$otherSchema = new OtherFormSchematic();
$this->remoteSchema($otherSchema);
```

### 2. Component Injection

```php
// Inject custom components into the form
$this->injectInput('custom_field', CustomComponent::make('custom_field'));
```

### 3. Attribute Mapping

```php
// Get mapped attributes with different layouts
$attributes = $this->returnMappedSchema('f'); // Flat array
$attributes = $this->returnMappedSchema('c'); // Card layout
$attributes = $this->returnMappedSchema('s'); // Section layout
$attributes = $this->returnMappedSchema('g.2'); // Grid with 2 columns
```

## Component Helper Functions

### Basic Components
- `textInput($attribute)` - Standard text input
- `selectInput($attribute)` - Select dropdown
- `checkboxInput($attribute)` - Checkbox
- `textAreaInput($attribute)` - Textarea

### Specialized Components
- `nationalCodeInput($attribute)` - Iranian national code validation
- `phoneNumberInput($attribute)` - Persian phone number input with validation
- `internationalPhoneNumberInput($attribute)` - International phone format
- `shebaTextInput($attribute)` - IBAN/Sheba validation
- `paymentCardTextInput($attribute)` - Bank card number validation

### Numeric Components
- `amountInput($attribute)` - Money input with Rial conversion
- `percentageInput($attribute)` - Percentage input (0-100)
- `month_input($attribute)` - Month counter

### Advanced Components
- `repeater($attribute)` - Repeater component with custom actions
- `datePickerInput($attribute)` - Jalali date picker (Iranian calendar)
- `richEditorInput($attribute)` - Rich text editor
- `tinyEditor($attribute)` - TinyMCE editor with RTL support

## Best Practices

### 1. Field Management
```php
// Always provide proper labels in Persian
public function attributeLabels(): array
{
    return [
        'first_name' => 'نام',
        'last_name' => 'نام خانوادگی',
        'mobile' => 'شماره همراه'
    ];
}

// Control visibility appropriately
public function invisibleAttributes(): array
{
    return [
        'id' => true,
        'created_at' => true,
        'updated_at' => true
    ];
}
```

### 2. Component Usage
```php
// Use the helper methods instead of direct component creation
// Good:
$this->textInput('name')
$this->nationalCodeInput('national_code')

// Avoid:
TextInput::make('name')->label('نام')
```

### 3. Form Schema Structure
```php
public function commonFormSchema(): array
{
    return [
        Section::make('Main Section')
            ->schema([
                Grid::make(2)
                    ->schema([
                        $this->textInput('first_name'),
                        $this->textInput('last_name')
                    ])
            ])
    ];
}
```

### 4. State Management
```php
public function disableAttributes(): array
{
    return [
        'id' => 'disabled_save', // Keep the ID in form data but don't show it
        'created_at' => 'disabled', // Hide and don't save (default)
    ];
}
```

## Example Implementation

### Complete Schematic Example
```php
class UserFormSchematic extends BaseFormSchematic
{
    public function attributeLabels(): array
    {
        return [
            'first_name' => 'نام',
            'last_name' => 'نام خانوادگی',
            'email' => 'ایمیل',
            'mobile' => 'شماره همراه'
        ];
    }

    public function commonFormSchema(): array
    {
        return [
            Section::make('اطلاعات کاربر')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            $this->textInput('first_name')
                                ->required(),
                            $this->textInput('last_name')
                                ->required(),
                            $this->emailInput('email')
                                ->required()
                                ->unique(),
                            $this->phoneNumberInput('mobile')
                                ->required()
                        ])
                ])
        ];
    }

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
            'id' => 'disabled_save',
            'created_at' => 'disabled'
        ];
    }
}
```

## Integration with Filament Pages

### Basic Page Integration
```php
class UserCreatePage extends BasePage implements HasForms
{
    public function form(Form $form): Form
    {
        return UserFormSchematic::makeForm($form, 'create')
            ->returnCreateForm();
    }
}
```

### Form Schema Variants
```php
// For common form (both create and edit)
return UserFormSchematic::makeForm($form, 'common')->returnCommonForm();

// For create-only form  
return UserFormSchematic::makeForm($form, 'create')->returnCreateForm();

// For edit-only form
return UserFormSchematic::makeForm($form, 'edit')->returnEditForm();
```

This pattern ensures consistent form handling across the application with centralized component management, proper labeling, and easy state control.
