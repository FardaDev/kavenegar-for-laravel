# Kavenegar API Endpoints Documentation

This document provides comprehensive documentation for all Kavenegar REST API endpoints.

## Base URL

```
https://api.kavenegar.com/v1/{API-KEY}/{method}.{format}
```

- `{API-KEY}`: Your Kavenegar API key
- `{method}`: The API method name (e.g., `sms/send`, `verify/lookup`)
- `{format}`: Response format (`json` or `xml`)

## Response Structure

All API responses follow this structure:

```json
{
    "return": {
        "status": 200,
        "message": "تایید شد"
    },
    "entries": [
        // Array of result objects
    ]
}
```

- `return.status`: HTTP status code (200 = success)
- `return.message`: Status message in Persian
- `entries`: Array of result objects (structure varies by endpoint)

---

## SMS Endpoints

### 1. Send (Simple SMS)

Send SMS to one or multiple recipients.

**Endpoint:** `sms/send.json`

**Method:** GET or POST

**Parameters:**

| Parameter | Required | Type | Description |
|-----------|----------|------|-------------|
| `receptor` | Yes | String | Recipient phone number(s), comma-separated for multiple |
| `message` | Yes | String | SMS message text (URL encoded) |
| `sender` | No | String | Sender line number (uses default if not provided) |
| `date` | No | UnixTime | Scheduled send time (sends immediately if empty) |
| `type` | No | Integer | Message display type (see Message Types table) |
| `localid` | No | Long | Local tracking ID for duplicate prevention |
| `hide` | No | Byte | Hide receptor in logs (1 = hide, 0 = show) |
| `tag` | No | String | Message tag for categorization (max 200 chars, alphanumeric + dash) |
| `policy` | No | String | Custom sending flow name |

**Response:**

```json
{
    "return": {"status": 200, "message": "تایید شد"},
    "entries": [
        {
            "messageid": 8792343,
            "message": "خدمات پیام کوتاه کاوه نگار",
            "status": 1,
            "statustext": "در صف ارسال",
            "sender": "10004346",
            "receptor": "09123456789",
            "date": 1356619709,
            "cost": 120
        }
    ]
}
```

**Response Fields:**

| Field | Type | Description |
|-------|------|-------------|
| `messageid` | Long | Unique message identifier |
| `message` | String | Sent message text |
| `status` | Integer | Message status code (see Status Codes table) |
| `statustext` | String | Status description |
| `sender` | String | Sender number |
| `receptor` | String | Recipient number |
| `date` | UnixTime | Send timestamp |
| `cost` | Integer | Message cost in Rials |

**Limits:**
- Maximum 200 messages per request
- Maximum 900 characters per message
- Messages over 70 chars (Persian) or 160 chars (Latin) are split into multiple parts

---

### 2. SendArray (Bulk SMS)

Send different messages to different recipients from different senders.

**Endpoint:** `sms/sendarray.json`

**Method:** POST (required)

**Parameters:**

| Parameter | Required | Type | Description |
|-----------|----------|------|-------------|
| `receptor` | Yes | Array[String] | Array of recipient phone numbers |
| `sender` | Yes | Array[String] | Array of sender line numbers |
| `message` | Yes | Array[String] | Array of message texts (URL encoded) |
| `date` | No | UnixTime | Scheduled send time |
| `type` | No | Array[Integer] | Array of message display types |
| `localmessageids` | No | Array[Long] | Array of local tracking IDs |
| `hide` | No | Byte | Hide receptors in logs |
| `tag` | No | String | Message tag |
| `policy` | No | String | Custom sending flow |

**Important:** All arrays must have equal length.

**Response:** Same structure as Send endpoint, returns array of message objects.

---

### 3. Status (Check Delivery Status)

Check delivery status of sent messages.

**Endpoint:** `sms/status.json`

**Method:** GET or POST

**Parameters:**

| Parameter | Required | Type | Description |
|-----------|----------|------|-------------|
| `messageid` | Yes | Long | Message ID(s), comma-separated for multiple |

**Response:**

```json
{
    "return": {"status": 200, "message": "تایید شد"},
    "entries": [
        {
            "messageid": 85463238,
            "status": 10,
            "statustext": "رسیده به گیرنده"
        }
    ]
}
```

**Limits:**
- Maximum 500 message IDs per request
- Only messages from last 48 hours are available

---

### 4. StatusLocalMessageId

Check status using local tracking ID.

**Endpoint:** `sms/statuslocalmessageid.json`

**Method:** GET or POST

**Parameters:**

| Parameter | Required | Type | Description |
|-----------|----------|------|-------------|
| `localid` | Yes | Long | Local tracking ID(s), comma-separated |

**Response:**

```json
{
    "return": {"status": 200, "message": "تایید شد"},
    "entries": [
        {
            "messageid": 85463238,
            "localid": "450",
            "status": 10,
            "statustext": "رسیده به گیرنده"
        }
    ]
}
```

**Limits:**
- Only messages from last 12 hours are available via localid

---

### 5. StatusByReceptor

Get list of messages sent to a specific phone number.

**Endpoint:** `sms/statusbyreceptor.json`

**Method:** GET or POST

**Parameters:**

| Parameter | Required | Type | Description |
|-----------|----------|------|-------------|
| `receptor` | Yes | String | Recipient phone number |
| `startdate` | Yes | UnixTime | Start of date range |
| `enddate` | No | UnixTime | End of date range (defaults to startdate + 1 day) |

**Response:**

```json
{
    "return": {"status": 200, "message": "تایید شد"},
    "entries": [
        {
            "messageid": 85463238,
            "receptor": "09123456789",
            "status": 10,
            "statustext": "رسیده به گیرنده"
        }
    ]
}
```

**Limits:**
- Maximum 1 day date range

---

### 6. Select (Get Message Details)

Retrieve full details of sent messages.

**Endpoint:** `sms/select.json`

**Method:** GET or POST

**Parameters:**

| Parameter | Required | Type | Description |
|-----------|----------|------|-------------|
| `messageid` | Yes | Long | Message ID(s), comma-separated |

**Response:** Same as Send endpoint with full message details.

**Limits:**
- Maximum 500 message IDs per request
- Requires IP whitelist configuration

---

### 7. SelectOutbox (List Sent Messages)

List messages sent within a date range.

**Endpoint:** `sms/selectoutbox.json`

**Method:** GET or POST

**Parameters:**

| Parameter | Required | Type | Description |
|-----------|----------|------|-------------|
| `startdate` | Yes | UnixTime | Start of date range |
| `enddate` | No | UnixTime | End of date range |
| `sender` | No | String | Filter by sender line |

**Response:** Array of message objects with full details.

**Limits:**
- Maximum 1 day date range
- Maximum 3 days in the past
- Maximum 500 results
- Requires IP whitelist configuration

---

### 8. LatestOutbox (Recent Messages)

Get most recent sent messages.

**Endpoint:** `sms/latestoutbox.json`

**Method:** GET or POST

**Parameters:**

| Parameter | Required | Type | Description |
|-----------|----------|------|-------------|
| `pagesize` | No | Integer | Number of messages to return (max 500, default 500) |
| `sender` | No | String | Filter by sender line |

**Response:** Array of most recent message objects.

**Limits:**
- Maximum 500 results
- Requires IP whitelist configuration

---

### 9. CountOutbox (Count Messages)

Count messages sent within a date range.

**Endpoint:** `sms/countoutbox.json`

**Method:** GET or POST

**Parameters:**

| Parameter | Required | Type | Description |
|-----------|----------|------|-------------|
| `startdate` | Yes | UnixTime | Start of date range |
| `enddate` | No | UnixTime | End of date range |
| `status` | No | Integer | Filter by message status |

**Response:**

```json
{
    "return": {"status": 200, "message": "تایید شد"},
    "entries": {
        "sumcount": 150
    }
}
```

**Limits:**
- Maximum 1 day date range

---

### 10. Cancel (Cancel Scheduled Messages)

Cancel messages that are queued or scheduled.

**Endpoint:** `sms/cancel.json`

**Method:** GET or POST

**Parameters:**

| Parameter | Required | Type | Description |
|-----------|----------|------|-------------|
| `messageid` | Yes | Long | Message ID(s) to cancel, comma-separated |

**Response:**

```json
{
    "return": {"status": 200, "message": "تایید شد"},
    "entries": [
        {
            "messageid": 8792343,
            "status": 13,
            "statustext": "لغو شده"
        }
    ]
}
```

**Limits:**
- Maximum 500 message IDs per request
- Only queued (status 1) or scheduled (status 2) messages can be cancelled

---

## Verification Endpoints

### 11. VerifyLookup (Template-based Verification)

Send verification codes using pre-defined templates.

**Endpoint:** `verify/lookup.json`

**Method:** GET or POST

**Parameters:**

| Parameter | Required | Type | Description |
|-----------|----------|------|-------------|
| `receptor` | Yes | String | Recipient phone number |
| `template` | Yes | String | Template name (must exist in panel) |
| `token` | Yes | String | First token value |
| `token2` | No | String | Second token value |
| `token3` | No | String | Third token value |
| `token10` | No | String | Tenth token value |
| `token20` | No | String | Twentieth token value |
| `type` | No | Integer | Message display type |

**Note:** Supports up to 20 tokens (token, token2, token3, ..., token20)

**Response:** Same as Send endpoint.

**Template Example:**
```
Your verification code is {token}. Valid for 5 minutes.
```

---

## Voice Call Endpoints

### 12. MakeTTS (Text-to-Speech Call)

Send text-to-speech voice call.

**Endpoint:** `call/maketts.json`

**Method:** GET or POST

**Parameters:**

| Parameter | Required | Type | Description |
|-----------|----------|------|-------------|
| `receptor` | Yes | String | Recipient phone number |
| `message` | Yes | String | Text to be spoken (URL encoded) |
| `date` | No | UnixTime | Scheduled call time |
| `localid` | No | Array[Long] | Local tracking IDs |

**Response:** Similar to SMS send response with call-specific fields.

---

## Account Endpoints

### 13. Info (Account Information)

Get account balance and details.

**Endpoint:** `account/info.json`

**Method:** GET or POST

**Parameters:** None

**Response:**

```json
{
    "return": {"status": 200, "message": "تایید شد"},
    "entries": {
        "remaincredit": 1000000,
        "expiredate": 1735689600,
        "type": "پیش پرداخت"
    }
}
```

**Response Fields:**

| Field | Type | Description |
|-------|------|-------------|
| `remaincredit` | Integer | Remaining credit in Rials |
| `expiredate` | UnixTime | Account expiry date |
| `type` | String | Account type |

---

### 14. Config (Account Configuration)

Get account configuration settings.

**Endpoint:** `account/config.json`

**Method:** GET or POST

**Parameters:** None

**Response:**

```json
{
    "return": {"status": 200, "message": "تایید شد"},
    "entries": {
        "apilogs": "enabled",
        "dailyreport": "enabled",
        "debugmode": "disabled",
        "defaultsender": "10004346",
        "mincreditalarm": 10000,
        "resendfailed": "enabled"
    }
}
```

---

## Reference Tables

### Message Status Codes

| Code | Description |
|------|-------------|
| 1 | در صف ارسال (In queue) |
| 2 | زمان بندی شده (Scheduled) |
| 4 | ارسال شده به مخابرات (Sent to operator) |
| 5 | ارسال شده به مخابرات (Sent to operator - duplicate of 4) |
| 6 | خطا در ارسال (Failed) |
| 10 | رسیده به گیرنده (Delivered) |
| 11 | نرسیده به گیرنده (Undelivered) |
| 13 | لغو شده (Cancelled) |
| 14 | بلاک شده (Blocked by recipient) |
| 100 | شناسه نامعتبر (Invalid ID) |

### Message Display Types

| Code | Description |
|------|-------------|
| 0 | Flash message (not saved) |
| 1 | Normal message (saved in phone memory) |
| 2 | Saved in SIM card |
| 3 | Saved in external application |

### Error Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 400 | Incomplete parameters |
| 401 | Invalid API key |
| 402 | Operation failed |
| 404 | Method not found |
| 405 | Wrong HTTP method (GET/POST) |
| 407 | Access denied (IP restriction) |
| 409 | Server unavailable |
| 411 | Invalid receptor number |
| 412 | Invalid sender number |
| 413 | Message empty or too long (max 900 chars) |
| 414 | Too many records (max 200 for send, 500 for status) |
| 417 | Invalid date format |
| 418 | Insufficient credit |
| 419 | Array length mismatch |
| 607 | Invalid tag name |

---

## Best Practices

### URL Encoding

Always URL encode message text and parameters, especially for:
- Persian/Unicode characters
- Special characters
- Spaces and newlines

### Duplicate Prevention

Use `localid` parameter to prevent duplicate sends:
- Assign unique ID from your database
- If same localid is sent again, API returns existing message without resending
- Useful for retry scenarios

### Rate Limiting

Respect API limits:
- Send: 200 messages per request
- Status: 500 IDs per request
- Use multi-threading for higher throughput

### Error Handling

Always check both:
1. HTTP status code (should be 200)
2. `return.status` in response (should be 200)

### IP Whitelisting

Some endpoints require IP whitelist:
- Select
- SelectOutbox
- LatestOutbox

Configure in panel: Settings > Security Settings

### Message Length

- Persian: 70 chars per part (67 for multi-part)
- Latin: 160 chars per part (153 for multi-part)
- Maximum total: 900 characters

### UnixTime Format

All dates use UnixTime (seconds since 1970-01-01):
```php
$timestamp = time(); // Current time
$scheduled = strtotime('+1 hour'); // 1 hour from now
```

---

## Examples

### Send Simple SMS

```
GET https://api.kavenegar.com/v1/YOUR-API-KEY/sms/send.json?receptor=09123456789&message=Hello&sender=10004346
```

### Send Bulk SMS

```
POST https://api.kavenegar.com/v1/YOUR-API-KEY/sms/sendarray.json

receptor=["09123456789","09987654321"]
sender=["10004346","10004346"]
message=["Message 1","Message 2"]
```

### Check Status

```
GET https://api.kavenegar.com/v1/YOUR-API-KEY/sms/status.json?messageid=123456,123457
```

### Send Verification Code

```
GET https://api.kavenegar.com/v1/YOUR-API-KEY/verify/lookup.json?receptor=09123456789&token=12345&template=verify
```

---

## Additional Resources

- Official Documentation: https://kavenegar.com/rest.html
- Panel: https://panel.kavenegar.com
- Support: support@kavenegar.com
