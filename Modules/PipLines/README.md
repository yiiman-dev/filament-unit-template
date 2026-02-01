# Pipeline Pattern for Cross-Service Operations

## Overview

The Pipeline pattern provides a clean and maintainable way to orchestrate operations that span multiple services. Each pipeline class encapsulates a specific workflow that requires coordination between different services, providing a single point of entry for complex business logic.

## Principles

1. **Single Responsibility**: Each pipeline handles one specific workflow or business process
2. **Error Propagation**: Pipelines follow the same error handling pattern as BaseService
3. **Standardized Interface**: All pipelines have a static `send()` method for consistent usage
4. **Panel-Specific Implementations**: Separate pipelines for My, Admin, and Manage panels to respect database separation

## Directory Structure

```
Modules/PipLines/
├── Common/
│   ├── UserRegistrationPipeline.php       # Handles user registration process
│   ├── UserVerificationPipeline.php       # Handles verification code validation
│   ├── PasswordResetPipeline.php          # Handles password reset workflow
│   ├── ManageUserRegistrationPipeline.php # For manage panel registrations
│   ├── AdminPasswordResetPipeline.php     # Admin-specific password reset
│   └── CorporateRegisteringApprove.php    # Corporate registration workflow
└── README.md
```

## Usage

### Basic Usage Pattern

```php
use FlowServices\Common\UserRegistrationPipeline;

// Get required services
$userService = app(UserService::class);
$smsService = app(BaseSmsService::class);

// Prepare data
$userData = [
    'name' => 'John Doe',
    'mobile' => '09123456789',
    'national_code' => '1234567890',
    'password' => 'secure_password'
];

// Execute pipeline
$result = UserRegistrationPipeline::send(
    $userService,
    $smsService,
    $userData
);

// Check for errors
if ($result->hasErrors()) {
    // Handle errors
    $errors = $result->getErrorMessages();
    Notification::make()->danger()->title('Registration failed')->body($errors[0])->send();
} else {
    // Success - access the success data
    $responseData = $result->getSuccessResponse();
    Notification::make()->success()->title('Registration successful')->send();
}
```

### Panel-Specific Pipelines

When working with different panels (My, Admin, Manage), use the appropriate pipeline class that works with the correct service implementations for that panel:

```php
// For My panel
use FlowServices\Common\UserRegistrationPipeline;
$myUserService = app(Units\Auth\My\Services\UserService::class);

// For Manage panel
use FlowServices\Common\ManageUserRegistrationPipeline;
$manageUserService = app(Units\Auth\Manage\Services\UserService::class);

// For Admin panel
use FlowServices\Common\AdminPasswordResetPipeline;
$adminUserService = app(Units\Auth\Admin\Services\UserService::class);
```

## Creating New Pipelines

When creating a new pipeline:

1. Extend `BaseService` for consistent error handling
2. Implement a `handle()` method that accepts all required services and data
3. Implement a static `send()` method with a variadic parameter to match the BaseService interface
4. Use `addError()` and `setSuccessResponse()` to manage operation state
5. Place the pipeline in the appropriate namespace `FlowServices\Common`

## Benefits

- **Reduced Code Duplication**: Common workflows are defined once
- **Improved Testability**: Pipelines can be unit tested in isolation
- **Simplified Controllers/Pages**: Filament pages become cleaner with less service orchestration code
- **Consistent Error Handling**: Unified approach to error handling across complex operations
- **Better Maintenance**: Changes to workflow logic are isolated to the pipeline class 