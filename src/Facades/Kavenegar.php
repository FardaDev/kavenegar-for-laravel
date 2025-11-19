<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Facades;

use FardaDev\Kavenegar\Dto\AccountConfig;
use FardaDev\Kavenegar\Dto\AccountInfo;
use FardaDev\Kavenegar\Dto\MessageResponse;
use FardaDev\Kavenegar\Dto\StatusResponse;
use FardaDev\Kavenegar\Requests\SendArrayRequest;
use FardaDev\Kavenegar\Requests\SendMessageRequest;
use FardaDev\Kavenegar\Requests\VerifyLookupRequest;
use Illuminate\Support\Facades\Facade;

/**
 * @method static MessageResponse[] send(SendMessageRequest $request)
 * @method static MessageResponse[] sendArray(SendArrayRequest $request)
 * @method static StatusResponse[] status(string|array<int, string> $messageid)
 * @method static StatusResponse[] statusLocalMessageId(string|array<int, string> $localid)
 * @method static StatusResponse[] statusByReceptor(string $receptor, int $startdate, ?int $enddate = null)
 * @method static MessageResponse[] select(string|array<int, string> $messageid)
 * @method static MessageResponse[] selectOutbox(int $startdate, ?int $enddate = null, ?string $sender = null)
 * @method static MessageResponse[] latestOutbox(?int $pagesize = null, ?string $sender = null)
 * @method static int countOutbox(int $startdate, ?int $enddate = null, ?int $status = null)
 * @method static MessageResponse[] cancel(string|array<int, string> $messageid)
 * @method static MessageResponse verifyLookup(VerifyLookupRequest $request)
 * @method static MessageResponse[] makeTTS(string $receptor, string $message, ?int $date = null, ?array<int, int> $localid = null)
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
