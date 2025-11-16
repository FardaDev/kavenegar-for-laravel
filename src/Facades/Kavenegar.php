<?php declare(strict_types=1);

namespace FardaDev\Kavenegar\Facades;

use FardaDev\Kavenegar\Dto\AccountConfig;
use FardaDev\Kavenegar\Dto\AccountInfo;
use FardaDev\Kavenegar\Dto\MessageResponse;
use FardaDev\Kavenegar\Dto\StatusResponse;
use Illuminate\Support\Facades\Facade;

/**
 * @method static MessageResponse[] send(string|array $receptor, string $message, ?string $sender = null, ?int $date = null, ?int $type = null, ?array $localid = null, ?int $hide = null, ?string $tag = null, ?string $policy = null)
 * @method static MessageResponse[] sendArray(array $senders, array $receptors, array $messages, ?int $date = null, ?array $types = null, ?array $localids = null, ?int $hide = null, ?string $tag = null, ?string $policy = null)
 * @method static StatusResponse[] status(string|array $messageid)
 * @method static StatusResponse[] statusLocalMessageId(string|array $localid)
 * @method static StatusResponse[] statusByReceptor(string $receptor, int $startdate, ?int $enddate = null)
 * @method static MessageResponse[] select(string|array $messageid)
 * @method static MessageResponse[] selectOutbox(int $startdate, ?int $enddate = null, ?string $sender = null)
 * @method static MessageResponse[] latestOutbox(?int $pagesize = null, ?string $sender = null)
 * @method static int countOutbox(int $startdate, ?int $enddate = null, ?int $status = null)
 * @method static MessageResponse[] cancel(string|array $messageid)
 * @method static MessageResponse verifyLookup(string $receptor, string $template, string $token, ?string $token2 = null, ?string $token3 = null, ?string $token10 = null, ?string $token20 = null, ?int $type = null)
 * @method static MessageResponse[] makeTTS(string $receptor, string $message, ?int $date = null, ?array $localid = null)
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
