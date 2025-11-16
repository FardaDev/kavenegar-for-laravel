# Kavenegar Laravel Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fardadev/kavenegar-laravel.svg?style=flat-square)](https://packagist.org/packages/fardadev/kavenegar-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/fardadev/kavenegar-laravel.svg?style=flat-square)](https://packagist.org/packages/fardadev/kavenegar-laravel)

Modern Laravel 12+ package for [Kavenegar](https://kavenegar.com) SMS API integration with full type safety, comprehensive testing, and Laravel best practices.

## Features

- ✅ **Laravel 12+ Compatible** - Built for modern Laravel with PHP 8.2+
- ✅ **Full Type Safety** - Strongly typed with readonly DTOs and return types
- ✅ **Auto-Discovery** - Automatic service provider and facade registration
- ✅ **Comprehensive API Coverage** - All Kavenegar endpoints supported
- ✅ **Exception Handling** - Typed exceptions for different error scenarios
- ✅ **Helper Methods** - Convenient helpers for common use cases
- ✅ **Environment Aware** - Skip SMS in development/testing environments
- ✅ **Fully Tested** - Comprehensive test coverage with Pest
- ✅ **Well Documented** - Complete API documentation and examples

## Requirements

- PHP 8.2 or higher
- Laravel 11.0 or 12.0
- Kavenegar API Key ([Get one here](https://panel.kavenegar.com/client/setting/account))

## Installation

Install the package via Composer:

```bash
composer require fardadev/kavenegar-laravel
```

The package will automatically register itself thanks to Laravel's auto-discovery feature.

### Publish Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=kavenegar-config
```

This will create a `config/kavenegar.php` file in your application.

### Environment Variables

Add your Kavenegar credentials to your `.env` file:

```env
KAVENEGAR_API_KEY=your-api-key-here
KAVENEGAR_SENDER=your-sender-number
KAVENEGAR_TIMEOUT=30
KAVENEGAR_SKIP_IN_DEV=true

# Optional: Template names for verification codes
KAVENEGAR_TEMPLATE_LOGIN=login-verify
KAVENEGAR_TEMPLATE_EMAIL_PASS=email-pass
KAVENEGAR_TEMPLATE_2FA=email-2fa
```


## Configuration

The `config/kavenegar.php` file contains all package configuration options:

```php
return [
    // Your Kavenegar API key (required)
    'api_key' => env('KAVENEGAR_API_KEY', ''),
    
    // Default sender line number (optional)
    'sender' => env('KAVENEGAR_SENDER', null),
    
    // HTTP request timeout in seconds
    'timeout' => env('KAVENEGAR_TIMEOUT', 30),
    
    // Skip SMS sending in local/dev environments
    'skip_in_development' => env('KAVENEGAR_SKIP_IN_DEV', true),
    
    // Test phone numbers (SMS skipped in testing environment)
    'test_phone_numbers' => [
        '09112223344',
    ],
    
    // Verification templates (must be created in Kavenegar panel)
    'templates' => [
        'login' => env('KAVENEGAR_TEMPLATE_LOGIN', 'login-verify'),
        'email_password' => env('KAVENEGAR_TEMPLATE_EMAIL_PASS', 'email-pass'),
        'two_factor' => env('KAVENEGAR_TEMPLATE_2FA', 'email-2fa'),
    ],
];
```

### Configuration Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `api_key` | string | - | Your Kavenegar API key (required) |
| `sender` | string\|null | null | Default sender line number |
| `timeout` | int | 30 | HTTP request timeout in seconds |
| `skip_in_development` | bool | true | Skip SMS in local/dev environments |
| `test_phone_numbers` | array | [] | Phone numbers treated as test numbers |
| `templates` | array | [] | Template names for verification codes |


## Usage

### Basic SMS Sending

#### Using Facade

```php
use FardaDev\Kavenegar\Facades\Kavenegar;

// Send to single recipient
$result = Kavenegar::send(
    receptor: '09123456789',
    message: 'Hello from Kavenegar!'
);

// Send to multiple recipients
$result = Kavenegar::send(
    receptor: ['09123456789', '09987654321'],
    message: 'Hello everyone!'
);

// Send with all options
$result = Kavenegar::send(
    receptor: '09123456789',
    message: 'Scheduled message',
    sender: '10004346',
    date: time() + 3600, // Send in 1 hour
    type: 1, // Save to phone memory
    localid: [123], // For duplicate prevention
    hide: 1, // Hide receptor in logs
    tag: 'marketing',
    policy: 'custom-flow'
);

// Check result
foreach ($result as $response) {
    echo "Message ID: {$response->messageid}\n";
    echo "Status: {$response->statustext}\n";
    echo "Cost: {$response->cost} Rials\n";
    
    if ($response->isDelivered()) {
        echo "Message delivered!\n";
    } elseif ($response->isPending()) {
        echo "Message is pending...\n";
    }
}
```

#### Using Dependency Injection

```php
use FardaDev\Kavenegar\Client\KavenegarClient;

class NotificationService
{
    public function __construct(private KavenegarClient $kavenegar) {}
    
    public function sendWelcomeSMS(string $phone): void
    {
        $this->kavenegar->send(
            receptor: $phone,
            message: 'Welcome to our service!'
        );
    }
}
```

### Bulk SMS Sending

Send different messages to different recipients from different senders:

```php
$result = Kavenegar::sendArray(
    senders: ['10004346', '10004347', '10004348'],
    receptors: ['09123456789', '09987654321', '09111111111'],
    messages: ['Message 1', 'Message 2', 'Message 3']
);
```

### Verification Codes

#### Using verifyLookup

```php
// Simple verification code
$result = Kavenegar::verifyLookup(
    receptor: '09123456789',
    template: 'login-verify',
    token: '123456'
);

// With multiple tokens
$result = Kavenegar::verifyLookup(
    receptor: '09123456789',
    template: 'email-pass',
    token: '123456',
    token2: 'user@example.com'
);

// With all token parameters
$result = Kavenegar::verifyLookup(
    receptor: '09123456789',
    template: 'custom-template',
    token: 'value1',
    token2: 'value2',
    token3: 'value3',
    token10: 'value10',
    token20: 'value20'
);
```

#### Using Helper Methods

```php
use FardaDev\Kavenegar\Helpers\KavenegarHelper;

$helper = app(KavenegarHelper::class);

// Send login code
$result = $helper->sendLoginCode('09123456789', '123456');

// Send email + password code
$result = $helper->sendEmailPasswordCode(
    '09123456789',
    '123456',
    'user@example.com'
);

// Send 2FA code
$result = $helper->sendTwoFactorCode(
    '09123456789',
    '654321',
    'user@example.com'
);
```

### Checking Message Status

```php
// Check by message ID
$status = Kavenegar::status('8792343');

// Check multiple messages
$status = Kavenegar::status(['8792343', '8792344']);

// Check by local ID
$status = Kavenegar::statusLocalMessageId('123');

// Check by receptor and date range
$status = Kavenegar::statusByReceptor(
    receptor: '09123456789',
    startdate: strtotime('-1 day'),
    enddate: time()
);

foreach ($status as $s) {
    echo "Message {$s->messageid}: {$s->statustext}\n";
    
    if ($s->isDelivered()) {
        echo "Delivered successfully!\n";
    }
}
```

### Message History and Selection

```php
// Get full message details
$messages = Kavenegar::select('8792343');

// Get messages in date range
$messages = Kavenegar::selectOutbox(
    startdate: strtotime('-1 day'),
    enddate: time(),
    sender: '10004346' // Optional filter
);

// Get latest messages
$messages = Kavenegar::latestOutbox(
    pagesize: 100,
    sender: '10004346' // Optional filter
);

// Count messages
$count = Kavenegar::countOutbox(
    startdate: strtotime('-1 day'),
    enddate: time(),
    status: 10 // Optional: only delivered messages
);
```

### Cancelling Messages

```php
// Cancel scheduled message
$result = Kavenegar::cancel('8792343');

// Cancel multiple messages
$result = Kavenegar::cancel(['8792343', '8792344']);
```

### Voice Calls (TTS)

```php
// Send text-to-speech call
$result = Kavenegar::makeTTS(
    receptor: '09123456789',
    message: 'Your verification code is 1 2 3 4 5 6'
);

// Scheduled TTS call
$result = Kavenegar::makeTTS(
    receptor: '09123456789',
    message: 'Reminder message',
    date: time() + 3600 // Call in 1 hour
);
```

### Account Information

```php
// Get account info
$info = Kavenegar::info();

echo "Credit: {$info->remaincredit} Rials\n";
echo "Expiry: " . $info->getExpiryDate()->format('Y-m-d') . "\n";

if ($info->hasCredit()) {
    echo "Account has credit\n";
}

if ($info->isExpired()) {
    echo "Account is expired!\n";
}

// Get account configuration
$config = Kavenegar::config();

if ($config->hasApiLogsEnabled()) {
    echo "API logs are enabled\n";
}
```


## Advanced Features

### Exception Handling

The package throws specific exceptions for different error scenarios:

```php
use FardaDev\Kavenegar\Exceptions\KavenegarApiException;
use FardaDev\Kavenegar\Exceptions\KavenegarHttpException;
use FardaDev\Kavenegar\Exceptions\KavenegarValidationException;

try {
    $result = Kavenegar::send('09123456789', 'Test message');
} catch (KavenegarValidationException $e) {
    // Input validation error (invalid phone, array mismatch, etc.)
    echo "Validation error: " . $e->getMessage();
    echo "Error code: " . $e->errorCode;
    dump($e->getContext());
} catch (KavenegarApiException $e) {
    // API returned error (401, 411, 418, etc.)
    echo "API error: " . $e->getMessage();
    echo "Error code: " . $e->errorCode;
    
    if ($e->errorCode === 418) {
        echo "Insufficient credit!";
    }
} catch (KavenegarHttpException $e) {
    // Network/connection error
    echo "Connection error: " . $e->getMessage();
}
```

### Environment-Based Testing

The package can skip SMS sending in development/testing environments:

```php
// In config/kavenegar.php
'skip_in_development' => true,

// SMS will be skipped in local/dev environments
$helper = app(KavenegarHelper::class);
$result = $helper->sendLoginCode('09123456789', '123456');
// Returns true instead of sending actual SMS

// Check if would skip
if ($helper->shouldSkipInDevelopment('09123456789')) {
    echo "SMS would be skipped in this environment";
}
```

### Privacy Features

Hide receptor numbers in logs and console:

```php
$result = Kavenegar::send(
    receptor: '09123456789',
    message: 'Sensitive message',
    hide: 1 // Receptor won't appear in sent message lists
);
```

### Duplicate Prevention

Use local IDs to prevent duplicate sends:

```php
// First send
$result = Kavenegar::send(
    receptor: '09123456789',
    message: 'Order #123 confirmed',
    localid: [123]
);

// Duplicate attempt - won't send again
$result = Kavenegar::send(
    receptor: '09123456789',
    message: 'Order #123 confirmed',
    localid: [123] // Same local ID
);
// Returns existing message details without resending
```

### Message Tagging

Organize messages with tags:

```php
$result = Kavenegar::send(
    receptor: '09123456789',
    message: 'Special offer!',
    tag: 'marketing-campaign-2024'
);
```

### Custom Sending Policies

Use custom sending flows (if configured in panel):

```php
$result = Kavenegar::send(
    receptor: '09123456789',
    message: 'High priority message',
    policy: 'high-priority-flow'
);
```

### Data Transfer Objects (DTOs)

All responses are strongly-typed readonly objects:

```php
$result = Kavenegar::send('09123456789', 'Test');

// MessageResponse DTO
$response = $result[0];
$response->messageid;   // int
$response->message;     // string
$response->status;      // int
$response->statustext;  // string
$response->sender;      // string
$response->receptor;    // string
$response->date;        // int (UnixTime)
$response->cost;        // int (Rials)

// Helper methods
$response->isDelivered(); // bool
$response->isFailed();    // bool
$response->isPending();   // bool

// AccountInfo DTO
$info = Kavenegar::info();
$info->hasCredit();       // bool
$info->isExpired();       // bool
$info->getCreditAmount(); // int
$info->getExpiryDate();   // DateTime
```

## Error Codes

Common error codes you might encounter:

| Code | Description | Exception Type |
|------|-------------|----------------|
| 200 | Success | - |
| 400 | Incomplete parameters | ValidationException |
| 401 | Invalid API key | ApiException |
| 402 | Operation failed | ApiException |
| 407 | Access denied (IP restriction) | ApiException |
| 411 | Invalid receptor number | ValidationException |
| 412 | Invalid sender number | ValidationException |
| 413 | Message too long or empty | ValidationException |
| 414 | Too many records | ValidationException |
| 418 | Insufficient credit | ApiException |
| 419 | Array length mismatch | ValidationException |

See [docs/error-codes.md](docs/error-codes.md) for complete error code reference.

## API Documentation

For detailed API documentation, see:

- [API Endpoints](docs/api-endpoints.md) - Complete endpoint reference
- [Error Codes](docs/error-codes.md) - Error code reference and troubleshooting
- [Migration Guide](docs/migration-guide.md) - Migrating from old package

## Testing

```bash
composer test
```

## Static Analysis

```bash
composer analyse
```

## Code Formatting

```bash
composer format
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for recent changes.

## Contributing

Contributions are welcome! Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email dev@farda.dev instead of using the issue tracker.

## Credits

- [FardaDev](https://github.com/fardadev)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Support

- [Kavenegar Documentation](https://kavenegar.com/rest.html)
- [GitHub Issues](https://github.com/fardadev/kavenegar-laravel/issues)
- Email: dev@farda.dev
