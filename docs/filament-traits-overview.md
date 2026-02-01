# Filament Traits Overview

## HasSMS
SMS sending in Filament
- `SendSmsToCurrentUser($text)`: Send to current user
- `SendSmsToCurrentCorporateCEO($text)`: Send to CEO

## HasNotification  
Display notifications
- `alert_success()`, `alert_error()`, `alert_info()`, `alert_warning()`

## InteractWithCorporate
Interaction with corporations
- `getCorporateModel()`, `getCorporateUsers()`, `getCorporateCEOModel()`

## HasError
Error management
- `addError()`, `hasErrors()`, `getErrors()`

## HasUUID
Automatic UUID generation
- `get_uuid_attributes()`: Define UUID fields

## HasAttributeLabels
Field label management
- `attributeLabels()`, `attributeHints()`

## CheckPageStandards
Development standards checking
- Check for Figma link in comments

## InteractWithLog
Logging
- `logInfo()`, `logError()`, `logWarning()`
