# Upload files as array problem
If you have save uploaded files problem on actions, this is because we have two type of data on forms:
```php
# True data for saving on DB
$livewire->form->getState()
```

```php
# Malformed data (will be save but hase problem on read)
$livewire->data
```
