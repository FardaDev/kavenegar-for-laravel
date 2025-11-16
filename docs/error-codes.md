# Kavenegar Error Codes Reference

This document provides comprehensive information about all error codes returned by the Kavenegar API and how to handle them.

## Error Response Structure

When an error occurs, the API returns a response with a non-200 status code:

```json
{
    "return": {
        "status": 411,
        "message": "شماره گیرنده پیام معتبر نمی باشد"
    }
}
```

## Error Code Categories

### Success Codes

| Code | Message | Description |
|------|---------|-------------|
| 200 | تایید شد | Request completed successfully |

### Client Error Codes (4xx)

| Code | Message | Description | Exception Type | Solution |
|------|---------|-------------|----------------|----------|
| 400 | پارامترها ناقص هستند | Incomplete parameters | `ValidationException` | Check all required parameters are provided |
| 401 | کلید API نامعتبر است | Invalid API key | `ApiException` | Verify API key in config |
| 402 | عملیات ناموفق بود | Operation failed | `ApiException` | Check request parameters and try again |
| 404 | متدی با این نام پیدا نشده است | Method not found | `ApiException` | Verify endpoint URL is correct |
| 405 | متد فراخوانی Get یا Post اشتباه است | Wrong HTTP method | `ApiException` | Use correct HTTP method (GET/POST) |
| 407 | دسترسی به اطلاعات مورد نظر برای شما امکان پذیر نیست | Access denied | `ApiException` | Configure IP whitelist in panel |
| 409 | سرور قادر به پاسخگوئی نیست | Server unavailable | `HttpException` | Retry after a delay |
| 411 | شماره گیرنده پیام معتبر نمی باشد | Invalid receptor number | `ValidationException` | Validate phone number format (09xxxxxxxxx) |
| 412 | شماره فرستنده معتبر نمی‌باشد | Invalid sender number | `ValidationException` | Use valid sender line from your account |
| 413 | پیام خالی است و یا طول پیام بیش از حد مجاز می‌باشد | Message empty or too long | `ValidationException` | Check message length (max 900 chars) |
| 414 | حجم درخواست بیشتر از حد مجاز است | Too many records | `ValidationException` | Reduce batch size (max 200 for send, 500 for status) |
| 417 | تاریخ معتبر نمی‌باشد | Invalid date | `ValidationException` | Use valid UnixTime format |
| 418 | اعتبار کافی نیست | Insufficient credit | `ApiException` | Add credit to your account |
| 419 | تعداد اعضای آرایه متن و گیرنده و ارسال کننده هم اندازه نیست | Array length mismatch | `ValidationException` | Ensure all arrays have equal length in sendArray |
| 607 | نام تگ انتخابی اشتباه است | Invalid tag name | `ValidationException` | Use valid tag format (alphanumeric + dash, max 200 chars) |

### Network Error Codes

| Error | Description | Exception Type | Solution |
|-------|-------------|----------------|----------|
| Connection Timeout | Request took too long | `HttpException` | Increase timeout or check network |
| DNS Resolution Failed | Cannot resolve api.kavenegar.com | `HttpException` | Check DNS settings |
| Connection Refused | Cannot connect to server | `HttpException` | Check firewall and network |
| SSL/TLS Error | Certificate validation failed | `HttpException` | Update CA certificates |

## Detailed Error Descriptions

### 400 - Incomplete Parameters

**Cause:** One or more required parameters are missing from the request.

**Common Scenarios:**
- Missing `receptor` in send request
- Missing `message` in send request
- Missing `messageid` in status request
- `pagesize` > 500 in latestOutbox

**Solution:**
```php
// ❌ Wrong - missing message
$client->send('09123456789', '');

// ✅ Correct
$client->send('09123456789', 'Hello World');
```

---

### 401 - Invalid API Key

**Cause:** The API key in the request URL is invalid or doesn't exist.

**Common Scenarios:**
- Typo in API key
- Using test API key in production
- API key revoked or expired

**Solution:**
```php
// Check your .env file
KAVENEGAR_API_KEY=your-actual-api-key-here

// Verify in panel: https://panel.kavenegar.com/client/setting/account
```

---

### 407 - Access Denied (IP Restriction)

**Cause:** Your IP address is not whitelisted for sensitive endpoints.

**Affected Endpoints:**
- `select`
- `selectOutbox`
- `latestOutbox`

**Solution:**
1. Go to Panel > Settings > Security Settings
2. Add your server's IP address to whitelist
3. Wait a few minutes for changes to propagate

---

### 411 - Invalid Receptor Number

**Cause:** Phone number format is invalid.

**Valid Format:**
- Must start with `09`
- Must be 11 digits
- Example: `09123456789`

**Common Mistakes:**
```php
// ❌ Wrong formats
'9123456789'    // Missing leading 0
'+989123456789' // Has country code
'0912 345 6789' // Has spaces
'0912-345-6789' // Has dashes

// ✅ Correct format
'09123456789'
```

**Solution:**
```php
// Normalize phone numbers
$phone = preg_replace('/[^0-9]/', '', $phone); // Remove non-digits
if (str_starts_with($phone, '98')) {
    $phone = '0' . substr($phone, 2); // Convert +98 to 0
}
```

---

### 412 - Invalid Sender Number

**Cause:** Sender line doesn't exist or doesn't belong to your account.

**Common Scenarios:**
- Using a line you haven't purchased
- Typo in sender number
- Line expired or suspended

**Solution:**
```php
// Check available lines in panel
// Use one of your active lines or set default in config

// config/kavenegar.php
'sender' => env('KAVENEGAR_SENDER', '10004346'),
```

---

### 413 - Message Empty or Too Long

**Cause:** Message text is empty or exceeds 900 characters.

**Limits:**
- Minimum: 1 character
- Maximum: 900 characters total
- Persian: 70 chars per part (67 for multi-part)
- Latin: 160 chars per part (153 for multi-part)

**Solution:**
```php
// Validate message length
if (empty($message)) {
    throw new ValidationException('Message cannot be empty');
}

if (mb_strlen($message) > 900) {
    throw new ValidationException('Message too long (max 900 chars)');
}
```

---

### 414 - Too Many Records

**Cause:** Batch size exceeds API limits.

**Limits:**
- Send/SendArray: 200 messages per request
- Status/Cancel: 500 IDs per request

**Solution:**
```php
// Chunk large batches
$receptors = [...]; // 1000 receptors
$chunks = array_chunk($receptors, 200);

foreach ($chunks as $chunk) {
    $client->send($chunk, $message);
}
```

---

### 417 - Invalid Date

**Cause:** Date parameter is not in valid UnixTime format or is in the past.

**Valid Format:**
- UnixTime (seconds since 1970-01-01)
- Must be in the future for scheduled messages

**Solution:**
```php
// ❌ Wrong
$date = '2024-01-01'; // String format
$date = time() - 3600; // Past time

// ✅ Correct
$date = time() + 3600; // 1 hour from now
$date = strtotime('+1 day'); // Tomorrow
```

---

### 418 - Insufficient Credit

**Cause:** Account doesn't have enough credit to send messages.

**Solution:**
1. Check account balance:
```php
$info = $client->info();
echo "Credit: " . $info->remaincredit . " Rials";
```

2. Add credit in panel: https://panel.kavenegar.com/client/credit/buy

3. Set up low credit alerts in panel settings

---

### 419 - Array Length Mismatch

**Cause:** In `sendArray`, the arrays for senders, receptors, and messages have different lengths.

**Solution:**
```php
// ❌ Wrong - mismatched lengths
$senders = ['10004346', '10004347'];
$receptors = ['09123456789'];
$messages = ['Message 1', 'Message 2'];

// ✅ Correct - all same length
$senders = ['10004346', '10004347'];
$receptors = ['09123456789', '09987654321'];
$messages = ['Message 1', 'Message 2'];

$client->sendArray($senders, $receptors, $messages);
```

---

### 607 - Invalid Tag Name

**Cause:** Tag format doesn't meet requirements.

**Requirements:**
- Maximum 200 characters
- Only alphanumeric characters (English)
- Can include dash (-) and underscore (_)
- No spaces
- No special characters (&, *, etc.)

**Solution:**
```php
// ❌ Wrong tags
'my tag'        // Has space
'tag@2024'      // Has special char
'تگ'            // Persian characters

// ✅ Correct tags
'my-tag'
'tag_2024'
'campaign-001'
```

---

## Exception Handling in Package

The package maps error codes to specific exception types:

```php
use FardaDev\Kavenegar\Exceptions\KavenegarApiException;
use FardaDev\Kavenegar\Exceptions\KavenegarHttpException;
use FardaDev\Kavenegar\Exceptions\KavenegarValidationException;

try {
    $result = $client->send('09123456789', 'Hello');
} catch (KavenegarValidationException $e) {
    // Handle validation errors (411, 412, 413, 414, 417, 419, 607)
    Log::warning('Invalid input', [
        'code' => $e->errorCode,
        'message' => $e->getMessage(),
        'context' => $e->getContext()
    ]);
} catch (KavenegarApiException $e) {
    // Handle API errors (401, 402, 418, etc.)
    Log::error('API error', [
        'code' => $e->errorCode,
        'message' => $e->getMessage()
    ]);
} catch (KavenegarHttpException $e) {
    // Handle network errors (timeout, connection, etc.)
    Log::error('Network error', [
        'message' => $e->getMessage()
    ]);
}
```

## Troubleshooting Guide

### Problem: Getting 401 errors

**Checklist:**
- [ ] Verify API key in `.env` file
- [ ] Check API key in panel (Settings > Account)
- [ ] Ensure no extra spaces in API key
- [ ] Try regenerating API key

### Problem: Getting 407 errors

**Checklist:**
- [ ] Add server IP to whitelist in panel
- [ ] Wait 5-10 minutes after adding IP
- [ ] Verify you're using correct IP (check with `curl ifconfig.me`)
- [ ] Check if using proxy or load balancer

### Problem: Getting 411 errors

**Checklist:**
- [ ] Phone number starts with `09`
- [ ] Phone number is exactly 11 digits
- [ ] No spaces, dashes, or special characters
- [ ] Not using country code (+98)

### Problem: Getting 418 errors

**Checklist:**
- [ ] Check account balance in panel
- [ ] Verify message cost (Persian vs Latin)
- [ ] Consider multi-part message costs
- [ ] Add credit if needed

### Problem: Messages not delivering (status 11)

**Possible Causes:**
- Recipient phone is off
- Recipient is out of coverage
- Recipient's inbox is full
- Number is invalid or deactivated

**Note:** Status 11 is not an error - message was sent but couldn't be delivered to recipient.

## Best Practices

### 1. Validate Before Sending

```php
// Validate inputs before API call
if (!preg_match('/^09\d{9}$/', $receptor)) {
    throw new ValidationException('Invalid phone number format');
}

if (mb_strlen($message) > 900) {
    throw new ValidationException('Message too long');
}
```

### 2. Handle Errors Gracefully

```php
try {
    $result = $client->send($receptor, $message);
} catch (KavenegarValidationException $e) {
    // Log and show user-friendly message
    return back()->withErrors('Invalid phone number or message');
} catch (KavenegarApiException $e) {
    if ($e->errorCode === 418) {
        // Specific handling for insufficient credit
        Mail::to('admin@example.com')->send(new LowCreditAlert());
    }
    throw $e;
}
```

### 3. Implement Retry Logic

```php
$maxRetries = 3;
$attempt = 0;

while ($attempt < $maxRetries) {
    try {
        return $client->send($receptor, $message);
    } catch (KavenegarHttpException $e) {
        $attempt++;
        if ($attempt >= $maxRetries) {
            throw $e;
        }
        sleep(2 ** $attempt); // Exponential backoff
    }
}
```

### 4. Monitor Credit

```php
// Check credit before bulk operations
$info = $client->info();
$estimatedCost = count($receptors) * 120; // Estimate cost

if ($info->remaincredit < $estimatedCost) {
    throw new InsufficientCreditException('Not enough credit for this operation');
}
```

## Support

If you encounter errors not covered in this document:

1. Check official documentation: https://kavenegar.com/rest.html
2. Contact Kavenegar support: support@kavenegar.com
3. Check panel for service status: https://status.kavenegar.com
4. Review API logs in panel: Settings > API Logs
