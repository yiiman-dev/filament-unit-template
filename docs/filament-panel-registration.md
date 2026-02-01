# Filament Panel Registration System

This document describes how to use the Filament panel registration system in your Laravel application.

## Overview

The `RetrieveFilamentProviderTrait` provides a unified way to register all resources for Filament panels, including:
- Views
- Blade Components
- Livewire Components
- Translations
- Configurations
- Console Commands

## Installation

The trait is automatically included in your application. No additional installation is required.

## Usage

### Basic Usage

In your Filament panel service provider, use the trait and call the registration method:

```php
use Modules\Basic\Concerns\RetrieveFilamentProviderTrait;

class YourPanelServiceProvider extends ServiceProvider
{
    use RetrieveFilamentProviderTrait;

    public function boot(): void
    {
        $this->registerFilamentPanel('Admin'); // or 'My', 'Manage', etc.
    }
}
```

### Directory Structure

Your units should follow this structure:

```
Modules/
└── Units/
    └── YourUnit/
        └── Admin/  # Panel directory
            ├── resources/
            │   ├── assets/
            │   ├── views/
            │   ├── components/
            │   └── lang/
            ├── Public/
            ├── config/
            └── Console/
                └── Commands/
```

### Namespacing

Resources are automatically namespaced using the pattern:
```
{panel_name}_{unit_name}
```

For example:
- Views: `admin_auth::view-name`
- Components: `admin_auth::component-name`
- Translations: `admin_auth::translation-key`
- Configs: `admin_auth.config-key`
- Public Asset mount::Published Asset: `Modules/Units/Auth/My/Public/css/style.css`=>`public/my/units/auth/css/style.css`

For `common` directories that maybe placed into `Modules/units/[Unit]/common` directory we have this naming namespace:
- views: `common_[unit]::view-name`

## Features

### Automatic Resource Discovery

The trait automatically discovers and registers:
- All views in `resources/views`
- All Blade components in `resources/components`
- All Livewire components in `Livewire/`
- All translations in `resources/lang`
- All config files in `config`
- All console commands in `Console/Commands`
- All public assets in `Public/`

### Publishing Resources

In console mode (e.g., during deployment), resources are automatically published to:
- Views: `resources/views/modules/{namespace}`
- Translations: `resources/lang/modules/{namespace}`
- Configs: `config/{namespace}`

### Component Registration

Blade components are automatically registered with their proper namespaces, allowing you to use them like:
```blade
<x-admin_auth::component-name />
```

### Livewire Component Registration

Livewire components are automatically discovered and registered from the `Livewire/` directory. The system will:

1. **Discover Components**: Automatically find all PHP files in the `Livewire/` directory
2. **Register Components**: Register each Livewire component with a name derived from the class name
3. **Automatic Naming**: Component names are created by converting the class name to lowercase and removing the 'Component' suffix

**Directory Structure:**
```
Modules/
└── Units/
    └── YourUnit/
        └── Admin/ # Panel directory
            └── Livewire/
                ├── ChatComponent.php
                ├── UserTableComponent.php
                └── DashboardComponent.php
```

**Component Naming Convention:**
- `ChatComponent.php` → `chat` (used as `<livewire:chat>`)
- `UserTableComponent.php` → `usertable` (used as `<livewire:usertable>`)
- `DashboardComponent.php` → `dashboard` (used as `<livewire:dashboard>`)

**Usage in Blade Templates:**
```blade
<livewire:chat />
<livewire:usertable />
<livewire:dashboard />
```

**Or using the @livewire directive:**
```blade
@livewire('chat')
@livewire('usertable')
@livewire('dashboard')
```

## Best Practices

1. **Panel Names**: Use consistent panel names across your application (e.g., 'Admin', 'My', 'Manage')
2. **Unit Organization**: Keep related functionality within the same unit
3. **Resource Location**: Follow the standard directory structure for resources
4. **Namespacing**: Use the provided namespacing convention to avoid conflicts

## Example

```php
// In your panel service provider
use Modules\Basic\Concerns\RetrieveFilamentProviderTrait;

class AdminPanelServiceProvider extends ServiceProvider
{
    use RetrieveFilamentProviderTrait;

    public function boot(): void
    {
        $this->registerFilamentPanel('Admin');
    }
}

// Using registered resources
// In a Blade view
<x-admin_auth::user-profile />

// In a controller
config('admin_auth.settings');

// In a translation file
__('admin_auth::messages.welcome');
```

## Troubleshooting

### Common Issues

1. **Resources Not Found**
   - Check if the panel name matches exactly
   - Verify the directory structure
   - Clear the view cache: `php artisan view:clear`

2. **Component Not Registered**
   - Verify the component namespace
   - Check if the component class exists
   - Clear the view cache

3. **Config Not Loaded**
   - Check the config file location
   - Verify the config key format
   - Clear the config cache: `php artisan config:clear`

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.
