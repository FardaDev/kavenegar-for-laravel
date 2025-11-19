<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Facades;

use FardaDev\Kavenegar\Dto\AccountConfig;
use FardaDev\Kavenegar\Dto\AccountInfo;
use FardaDev\Kavenegar\Dto\MessageResponse;
use FardaDev\Kavenegar\Dto\StatusResponse;
use FardaDev\Kavenegar\Requests\CancelRequest;
use FardaDev\Kavenegar\Requests\CountOutboxRequest;
use FardaDev\Kavenegar\Requests\LatestOutboxRequest;
use FardaDev\Kavenegar\Requests\SelectOutboxRequest;
use FardaDev\Kavenegar\Requests\SelectRequest;
use FardaDev\Kavenegar\Requests\SendArrayRequest;
use FardaDev\Kavenegar\Requests\SendMessageRequest;
use FardaDev\Kavenegar\Requests\StatusByReceptorRequest;
use FardaDev\Kavenegar\Requests\StatusRequest;
use FardaDev\Kavenegar\Requests\VerifyLookupRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Collection<int, MessageResponse> send(SendMessageRequest $request)
 * @method static Collection<int, MessageResponse> sendArray(SendArrayRequest $request)
 * @method static Collection<int, StatusResponse> status(StatusRequest $request)
 * @method static Collection<int, StatusResponse> statusLocalMessageId(string|array<int, string> $localid)
 * @method static Collection<int, StatusResponse> statusByReceptor(StatusByReceptorRequest $request)
 * @method static Collection<int, MessageResponse> select(SelectRequest $request)
 * @method static Collection<int, MessageResponse> selectOutbox(SelectOutboxRequest $request)
 * @method static Collection<int, MessageResponse> latestOutbox(LatestOutboxRequest $request)
 * @method static int countOutbox(CountOutboxRequest $request)
 * @method static Collection<int, MessageResponse> cancel(CancelRequest $request)
 * @method static MessageResponse verifyLookup(VerifyLookupRequest $request)
 * @method static Collection<int, MessageResponse> makeTTS(string $receptor, string $message, ?int $date = null, ?array<int, int> $localid = null)
 * @method static AccountInfo info()
 * @method static AccountConfig config()
 *
 * @see \FardaDev\Kavenegar\Client\KavenegarClient
 */
class Kavenegar extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'kavenegar';
    }
}
