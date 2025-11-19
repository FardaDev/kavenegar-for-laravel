<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Facades;

use FardaDev\Kavenegar\Dto\AccountConfig;
use FardaDev\Kavenegar\Dto\AccountInfo;
use FardaDev\Kavenegar\Dto\MessageResponse;
use FardaDev\Kavenegar\Dto\StatusResponse;
use FardaDev\Kavenegar\Requests\SendMessageRequest;
use Illuminate\Support\Facades\Facade;

/**
 * @method static MessageResponse[] send(SendMessageRequest $request)
 * @method static MessageResponse[] sendArray(array<int, string> $senders, array<int, string> $receptors, array<int, string> $messages, ?int $date = null, ?array<int, int> $types = null, ?array<int, int> $localids = null, ?int $hide = null, ?string $tag = null, ?string $policy = null)
 * @method static StatusResponse[] status(string|array<int, string> $messageid)
 * @method static StatusResponse[] statusLocalMessageId(string|array<int, string> $localid)
 * @method static StatusResponse[] statusByReceptor(string $receptor, int $startdate, ?int $enddate = null)
 * @method static MessageResponse[] select(string|array<int, string> $messageid)
 * @method static MessageResponse[] selectOutbox(int $startdate, ?int $enddate = null, ?string $sender = null)
 * @method static MessageResponse[] latestOutbox(?int $pagesize = null, ?string $sender = null)
 * @method static int countOutbox(int $startdate, ?int $enddate = null, ?int $status = null)
 * @method static MessageResponse[] cancel(string|array<int, string> $messageid)
 * @method static MessageResponse verifyLookup(string $receptor, string $template, string $token, ?string $token2 = null, ?string $token3 = null, ?string $token10 = null, ?string $token20 = null, ?int $type = null)
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
