<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Client;

use FardaDev\Kavenegar\Dto\AccountConfig;
use FardaDev\Kavenegar\Dto\AccountInfo;
use FardaDev\Kavenegar\Dto\MessageResponse;
use FardaDev\Kavenegar\Dto\StatusResponse;
use FardaDev\Kavenegar\Exceptions\KavenegarApiException;
use FardaDev\Kavenegar\Exceptions\KavenegarHttpException;
use FardaDev\Kavenegar\Exceptions\KavenegarValidationException;
use FardaDev\Kavenegar\Requests\LatestOutboxRequest;
use FardaDev\Kavenegar\Requests\SendArrayRequest;
use FardaDev\Kavenegar\Requests\SendMessageRequest;
use FardaDev\Kavenegar\Requests\StatusRequest;
use FardaDev\Kavenegar\Requests\VerifyLookupRequest;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class KavenegarClient
{
    private const BASE_URL = 'https://api.kavenegar.com/v1';

    public function __construct(
        private readonly string $apiKey,
        private readonly ?string $defaultSender = null,
        private readonly int $timeout = 30
    ) {}

    /**
     * Build API URL for the given method.
     */
    private function buildUrl(string $method, string $format = 'json'): string
    {
        return sprintf('%s/%s/%s.%s', self::BASE_URL, $this->apiKey, $method, $format);
    }

    /**
     * Execute HTTP request to Kavenegar API.
     *
     * @param  array<string, mixed>  $params
     * @return array<int, array<string, mixed>>
     *
     * @throws KavenegarHttpException
     * @throws KavenegarApiException
     */
    private function executeRequest(string $url, array $params, string $httpMethod = 'GET'): array
    {
        try {
            $request = Http::timeout($this->timeout);

            $response = match ($httpMethod) {
                'POST' => $request->asForm()->post($url, $params),
                default => $request->get($url, $params),
            };

            return $this->handleResponse($response);
        } catch (ConnectionException $e) {
            throw new KavenegarHttpException(
                message: 'Failed to connect to Kavenegar API: '.$e->getMessage(),
                errorCode: 0,
                context: ['url' => $url, 'method' => $httpMethod],
                previous: $e
            );
        }
    }

    /**
     * Handle API response and check for errors.
     *
     * @return array<int, array<string, mixed>>
     *
     * @throws KavenegarApiException
     */
    private function handleResponse(Response $response): array
    {
        if (! $response->successful()) {
            throw new KavenegarHttpException(
                message: 'HTTP request failed with status '.$response->status(),
                errorCode: $response->status(),
                context: ['body' => $response->body()]
            );
        }

        $data = $response->json();

        if (! isset($data['return']['status'])) {
            throw new KavenegarApiException(
                message: 'Invalid API response format',
                errorCode: 0,
                context: ['response' => $data]
            );
        }

        $status = (int) $data['return']['status'];

        if ($status !== 200) {
            $message = $data['return']['message'] ?? 'Unknown error';
            throw new KavenegarApiException(
                message: $message,
                errorCode: $status,
                context: ['response' => $data]
            );
        }

        return $data['entries'] ?? [];
    }

    /**
     * Validate that all arrays have the same length.
     *
     * @param  array<int, mixed>  ...$arrays
     *
     * @throws KavenegarValidationException
     */
    private function validateArrayLengths(array ...$arrays): void
    {
        $lengths = array_map('count', $arrays);
        $uniqueLengths = array_unique($lengths);

        if (count($uniqueLengths) > 1) {
            throw new KavenegarValidationException(
                message: 'All arrays must have the same length',
                errorCode: 419,
                context: ['lengths' => $lengths]
            );
        }
    }

    /**
     * Convert array or string to comma-separated string.
     *
     * @param  string|array<int, string|int>  $value
     */
    private function toCommaSeparated(string|array $value): string
    {
        if (is_array($value)) {
            return implode(',', $value);
        }

        return $value;
    }

    /**
     * Validate tag format.
     *
     * @throws KavenegarValidationException
     */
    private function validateTag(?string $tag): void
    {
        if ($tag === null) {
            return;
        }

        if (strlen($tag) > 200) {
            throw new KavenegarValidationException(
                message: 'Tag must not exceed 200 characters',
                errorCode: 607,
                context: ['tag' => $tag, 'length' => strlen($tag)]
            );
        }

        if (! preg_match('/^[a-zA-Z0-9_-]+$/', $tag)) {
            throw new KavenegarValidationException(
                message: 'Tag must contain only alphanumeric characters, hyphens, and underscores',
                errorCode: 607,
                context: ['tag' => $tag]
            );
        }
    }

    /**
     * Send SMS to one or more recipients.
     *
     * @return array<int, MessageResponse>
     *
     * @throws KavenegarValidationException
     * @throws KavenegarApiException
     * @throws KavenegarHttpException
     */
    public function send(SendMessageRequest $request): array
    {
        $params = $request->toApiParams();

        if (! isset($params['sender']) && $this->defaultSender !== null) {
            $params['sender'] = $this->defaultSender;
        }

        $url = $this->buildUrl('sms/send');
        $entries = $this->executeRequest($url, $params);

        return array_map(
            fn (array $entry) => MessageResponse::fromArray($entry),
            $entries
        );
    }

    /**
     * Send multiple different messages to different recipients from different senders.
     *
     * @return MessageResponse[]
     *
     * @throws KavenegarValidationException
     * @throws KavenegarApiException
     * @throws KavenegarHttpException
     */
    public function sendArray(SendArrayRequest $request): array
    {
        $params = $request->toApiParams();

        $url = $this->buildUrl('sms/sendarray');
        $entries = $this->executeRequest($url, $params, 'POST');

        return array_map(
            fn (array $entry) => MessageResponse::fromArray($entry),
            $entries
        );
    }

    /**
     * Check delivery status of sent messages by message ID.
     * Can check up to 500 messages per request.
     * Only works for messages sent within last 48 hours.
     *
     * @return StatusResponse[]
     *
     * @throws KavenegarValidationException
     * @throws KavenegarApiException
     * @throws KavenegarHttpException
     */
    public function status(StatusRequest $request): array
    {
        $params = $request->toApiParams();

        $url = $this->buildUrl('sms/status');
        $entries = $this->executeRequest($url, $params);

        return array_map(
            fn (array $entry) => StatusResponse::fromArray($entry),
            $entries
        );
    }

    /**
     * Check delivery status by local message ID.
     * Only works for messages sent within last 12 hours.
     *
     * @param  string|array<int, string>  $localid  Local ID(s) to check
     * @return StatusResponse[]
     *
     * @throws KavenegarApiException
     * @throws KavenegarHttpException
     */
    public function statusLocalMessageId(string|array $localid): array
    {
        $params = [
            'localid' => $this->toCommaSeparated($localid),
        ];

        $url = $this->buildUrl('sms/statuslocalmessageid');
        $entries = $this->executeRequest($url, $params);

        return array_map(
            fn (array $entry) => StatusResponse::fromArray($entry),
            $entries
        );
    }

    /**
     * Get list of messages sent to a specific receptor within a date range.
     * Maximum date range is 1 day.
     *
     * @param  string  $receptor  Recipient phone number
     * @param  int  $startdate  Start of date range (UnixTime)
     * @param  int|null  $enddate  End of date range (UnixTime, defaults to +1 day)
     * @return StatusResponse[]
     *
     * @throws KavenegarApiException
     * @throws KavenegarHttpException
     */
    public function statusByReceptor(
        string $receptor,
        int $startdate,
        ?int $enddate = null
    ): array {
        $params = [
            'receptor' => $receptor,
            'startdate' => $startdate,
        ];

        if ($enddate !== null) {
            $params['enddate'] = $enddate;
        }

        $url = $this->buildUrl('sms/statusbyreceptor');
        $entries = $this->executeRequest($url, $params);

        return array_map(
            fn (array $entry) => StatusResponse::fromArray($entry),
            $entries
        );
    }

    /**
     * Get full details of sent messages by message ID.
     * Can retrieve up to 500 messages per request.
     * Requires IP restriction configuration in panel.
     *
     * @param  string|array<int, string>  $messageid  Message ID(s) to retrieve
     * @return MessageResponse[]
     *
     * @throws KavenegarApiException
     * @throws KavenegarHttpException
     */
    public function select(string|array $messageid): array
    {
        $params = [
            'messageid' => $this->toCommaSeparated($messageid),
        ];

        $url = $this->buildUrl('sms/select');
        $entries = $this->executeRequest($url, $params);

        return array_map(
            fn (array $entry) => MessageResponse::fromArray($entry),
            $entries
        );
    }

    /**
     * List sent messages within a date range.
     * Maximum date range is 1 day. Start date must be within last 3 days.
     * Returns up to 500 messages. Requires IP restriction configuration.
     *
     * @param  int  $startdate  Start of date range (UnixTime)
     * @param  int|null  $enddate  End of date range (UnixTime, defaults to now)
     * @param  string|null  $sender  Filter by specific sender line
     * @return MessageResponse[]
     *
     * @throws KavenegarApiException
     * @throws KavenegarHttpException
     */
    public function selectOutbox(
        int $startdate,
        ?int $enddate = null,
        ?string $sender = null
    ): array {
        $params = [
            'startdate' => $startdate,
        ];

        if ($enddate !== null) {
            $params['enddate'] = $enddate;
        }

        if ($sender !== null) {
            $params['sender'] = $sender;
        }

        $url = $this->buildUrl('sms/selectoutbox');
        $entries = $this->executeRequest($url, $params);

        return array_map(
            fn (array $entry) => MessageResponse::fromArray($entry),
            $entries
        );
    }

    /**
     * Get the most recent sent messages.
     *
     * @return MessageResponse[]
     *
     * @throws KavenegarApiException
     * @throws KavenegarHttpException
     */
    public function latestOutbox(LatestOutboxRequest $request): array
    {
        $params = $request->toApiParams();

        $url = $this->buildUrl('sms/latestoutbox');
        $entries = $this->executeRequest($url, $params);

        return array_map(
            fn (array $entry) => MessageResponse::fromArray($entry),
            $entries
        );
    }

    /**
     * Count sent messages within a date range.
     * Maximum date range is 1 day.
     *
     * @param  int  $startdate  Start of date range (UnixTime)
     * @param  int|null  $enddate  End of date range (UnixTime, defaults to +1 day)
     * @param  int|null  $status  Filter by specific status code
     * @return int Number of messages
     *
     * @throws KavenegarApiException
     * @throws KavenegarHttpException
     */
    public function countOutbox(
        int $startdate,
        ?int $enddate = null,
        ?int $status = null
    ): int {
        $params = [
            'startdate' => $startdate,
        ];

        if ($enddate !== null) {
            $params['enddate'] = $enddate;
        }

        if ($status !== null) {
            $params['status'] = $status;
        }

        $url = $this->buildUrl('sms/countoutbox');
        $entries = $this->executeRequest($url, $params);

        return (int) ($entries[0]['count'] ?? 0);
    }

    /**
     * Cancel scheduled messages before they are sent.
     * Can only cancel messages in queue or scheduled status.
     * Can cancel up to 500 messages per request.
     *
     * @param  string|array<int, string>  $messageid  Message ID(s) to cancel
     * @return MessageResponse[]
     *
     * @throws KavenegarApiException
     * @throws KavenegarHttpException
     */
    public function cancel(string|array $messageid): array
    {
        $params = [
            'messageid' => $this->toCommaSeparated($messageid),
        ];

        $url = $this->buildUrl('sms/cancel');
        $entries = $this->executeRequest($url, $params);

        return array_map(
            fn (array $entry) => MessageResponse::fromArray($entry),
            $entries
        );
    }

    /**
     * Send verification code using pre-defined template.
     *
     * @throws KavenegarApiException
     * @throws KavenegarHttpException
     */
    public function verifyLookup(VerifyLookupRequest $request): MessageResponse
    {
        $params = $request->toApiParams();

        $url = $this->buildUrl('verify/lookup');
        $entries = $this->executeRequest($url, $params);

        return MessageResponse::fromArray($entries[0]);
    }

    /**
     * Send text-to-speech voice call.
     * Converts text message to voice and calls the recipient.
     *
     * @param  string  $receptor  Recipient phone number
     * @param  string  $message  Message text to convert to speech
     * @param  int|null  $date  Scheduled call time (UnixTime)
     * @param  array<int, int>|null  $localid  Local IDs for duplicate prevention
     * @return MessageResponse[]
     *
     * @throws KavenegarApiException
     * @throws KavenegarHttpException
     */
    public function makeTTS(
        string $receptor,
        string $message,
        ?int $date = null,
        ?array $localid = null
    ): array {
        $params = [
            'receptor' => $receptor,
            'message' => $message,
        ];

        if ($date !== null) {
            $params['date'] = $date;
        }

        if ($localid !== null) {
            $params['localid'] = $this->toCommaSeparated($localid);
        }

        $url = $this->buildUrl('call/maketts');
        $entries = $this->executeRequest($url, $params);

        return array_map(
            fn (array $entry) => MessageResponse::fromArray($entry),
            $entries
        );
    }

    /**
     * Get account information including credit balance and expiry date.
     *
     * @throws KavenegarApiException
     * @throws KavenegarHttpException
     */
    public function info(): AccountInfo
    {
        $url = $this->buildUrl('account/info');
        $entries = $this->executeRequest($url, []);

        return AccountInfo::fromArray($entries[0]);
    }

    /**
     * Get account configuration settings.
     *
     * @throws KavenegarApiException
     * @throws KavenegarHttpException
     */
    public function config(): AccountConfig
    {
        $url = $this->buildUrl('account/config');
        $entries = $this->executeRequest($url, []);

        return AccountConfig::fromArray($entries[0]);
    }
}
