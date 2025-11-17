# Migration Guide

This guide helps you migrate from the old `kavenegar/laravel` package to the new `fardadev/kavenegar-for-laravel` package.

## Why Migrate?

The new package offers:

- ✅ **Laravel 11+ compatibility** with modern PHP 8.2+ features
- ✅ **Type safety** with readonly DTOs and strict typing
- ✅ **Better error handling** with specific exception types
- ✅ **Comprehensive testing** with Pest framework

## Step 1: Update Composer Dependencies

Remove the old package and install the new one:

```bash
composer remove kavenegar/laravel
composer require fardadev/kavenegar-for-laravel
```

## Step 2: Remove Manual Service Provider Registration

If you manually registered the service provider in `config/app.php`, remove it. The new package uses Laravel's auto-discovery.

**Remove from `config/app.php`:**
```php
'providers' => [
    // Remove this line:
    Kavenegar\Laravel\ServiceProvider::class,
],

'aliases' => [
    // Remove this line:
    'Kavenegar' => Kavenegar\Laravel\Facade\Kavenegar::class,
],
```

## Step 3: Update Configuration

Publish the new configuration file:

```bash
php artisan vendor:publish --tag=kavenegar-config
```

Update your `.env` file with the new variable names:

**Old `.env`:**
```env
KAVENEGAR_APIKEY=your-api-key
```

**New `.env`:**
```env
KAVENEGAR_API_KEY=your-api-key
KAVENEGAR_SENDER=your-sender-number
KAVENEGAR_TIMEOUT=30
KAVENEGAR_SKIP_IN_DEV=true
```

## Step 4: Update Exception Handling

The new package uses specific exception types instead of generic exceptions.

**Old Code:**
```php
try {
    $result = Kavenegar::Send($sender, $receptor, $message);
} catch(\Kavenegar\Exceptions\ApiException $e) {
    echo $e->errorMessage();
} catch(\Kavenegar\Exceptions\HttpException $e) {
    echo $e->errorMessage();
}
```

**New Code:**
```php
use FardaDev\Kavenegar\Exceptions\KavenegarApiException;
use FardaDev\Kavenegar\Exceptions\KavenegarHttpException;

try {
    $result = Kavenegar::send($receptor, $message, $sender);
} catch (KavenegarApiException $e) {
    // API returned error (401, 411, 418, etc.)
    echo $e->getMessage();
    echo "Error code: " . $e->errorCode;
    dump($e->getContext());
} catch (KavenegarHttpException $e) {
    // Network/connection error
    echo $e->getMessage();
}
```

## Step 5: Update Response Handling

The new package returns strongly-typed DTOs instead of stdClass objects.

**Old Code:**
```php
$result = Kavenegar::Send($sender, $receptor, $message);
foreach($result as $r) {
    echo "messageid = $r->messageid";
    echo "status = $r->status";
}
```

**New Code:**
```php
use FardaDev\Kavenegar\Dto\MessageResponse;

$result = Kavenegar::send($receptor, $message, $sender);
foreach($result as $response) {
    /** @var MessageResponse $response */
    echo "messageid = {$response->messageid}";
    echo "status = {$response->status}";
    
    // Use helper methods
    if ($response->isDelivered()) {
        echo "Message delivered!";
    }
}
```

## Step 6: Update VerifyLookup Calls

Method signature has changed to use named parameters.

**Old Code:**
```php
$result = Kavenegar::VerifyLookup($receptor, $token, $token2, $token3, $template, $type);
```

**New Code:**
```php
$result = Kavenegar::verifyLookup(
    receptor: $receptor,
    template: $template,
    token: $token,
    token2: $token2,
    token3: $token3,
    type: $type
);

// Or use helper methods
use FardaDev\Kavenegar\Helpers\KavenegarHelper;

$helper = app(KavenegarHelper::class);
$result = $helper->sendLoginCode($receptor, $code);
```

## Step 7: Update Notification Usage (if applicable)

If you were using the notification channel, you'll need to update your notification classes.

**Old Code:**
```php
use Kavenegar\Laravel\Notification\KavenegarBaseNotification;
use Kavenegar\Laravel\Message\KavenegarMessage;

class InvoicePaid extends KavenegarBaseNotification
{
    public function toKavenegar($notifiable)
    {
        return (new KavenegarMessage('فاکتور شما پرداخت شد.'))
            ->from('10004346');
    }
}
```

**New Code:**
```php
// Notification channel support will be added in a future version
// For now, use the client directly in your notification:

use Illuminate\Notifications\Notification;
use FardaDev\Kavenegar\Facades\Kavenegar;

class InvoicePaid extends Notification
{
    public function via($notifiable)
    {
        return ['database']; // or other channels
    }
    
    public function toArray($notifiable)
    {
        // Send SMS directly
        Kavenegar::send(
            receptor: $notifiable->phone,
            message: 'فاکتور شما پرداخت شد.',
            sender: config('kavenegar.sender')
        );
        
        return [
            'invoice_id' => $this->invoice->id,
        ];
    }
}
```

## Common Issues and Solutions

### Issue: "Class 'Kavenegar' not found"

**Solution:** Make sure you've removed the old manual alias registration from `config/app.php`. The new package uses auto-discovery.

### Issue: "Call to undefined method errorMessage()"

**Solution:** The new exceptions use standard `getMessage()` method instead of `errorMessage()`.

```php
// Old
catch(\Kavenegar\Exceptions\ApiException $e) {
    echo $e->errorMessage();
}

// New
catch(KavenegarApiException $e) {
    echo $e->getMessage();
}
```

### Issue: "Trying to get property of non-object"

**Solution:** Make sure you're using the correct property names. DTOs use readonly properties.

```php
// Old
$result->messageid

// New (same, but with type safety)
$result->messageid // This is a readonly property
```

### Issue: "Method Send() not found"

**Solution:** Method names are now camelCase instead of PascalCase.

```php
// Old
Kavenegar::Send()
Kavenegar::VerifyLookup()

// New
Kavenegar::send()
Kavenegar::verifyLookup()
```

## Testing Your Migration

After migration, test these key scenarios:

1. **Send a simple SMS:**
```php
$result = Kavenegar::send('09123456789', 'Test message');
```

2. **Send verification code:**
```php
$result = Kavenegar::verifyLookup(
    receptor: '09123456789',
    template: 'login-verify',
    token: '123456'
);
```

3. **Check message status:**
```php
$status = Kavenegar::status($messageid);
```

4. **Handle exceptions:**
```php
try {
    $result = Kavenegar::send('invalid', 'test');
} catch (KavenegarValidationException $e) {
    // Handle validation error
} catch (KavenegarApiException $e) {
    // Handle API error
}
```

## Getting Help

If you encounter issues during migration:

1. Check the [API documentation](api-endpoints.md)
2. Review the [error codes](error-codes.md)
3. Check your configuration in `config/kavenegar.php`
4. Ensure your API key is valid
5. Open an issue on GitHub with details

## Rollback Plan

If you need to rollback to the old package:

1. Restore your `composer.json`:
```bash
composer remove fardadev/kavenegar-for-laravel
composer require kavenegar/laravel
```

2. Restore manual provider registration in `config/app.php`

3. Revert your code changes using version control

## Benefits of Migration

After migration, you'll benefit from:

- ✅ Full type safety with PHP 8.2+ features
- ✅ Better IDE autocomplete and type hints
- ✅ Comprehensive exception handling
- ✅ Modern Laravel 11+ compatibility
- ✅ Improved testing capabilities
- ✅ Better error messages and debugging
- ✅ Cleaner, more maintainable code
