<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Dto;

use FardaDev\Kavenegar\Enums\ApiErrorCodeEnum;
use FardaDev\Kavenegar\Exceptions\KavenegarApiException;
use Illuminate\Support\Facades\Validator;

readonly class AccountConfig
{
    public function __construct(
        public string $apilogs,
        public string $dailyreport,
        public string $debugmode,
        public string $defaultsender,
        public int $mincreditalarm,
        public string $resendfailed
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $validator = Validator::make($data, [
            'apilogs' => ['required', 'string'],
            'dailyreport' => ['required', 'string'],
            'debugmode' => ['required', 'string'],
            'defaultsender' => ['required', 'string'],
            'mincreditalarm' => ['required', 'integer', 'min:0'],
            'resendfailed' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            throw new KavenegarApiException(
                message: 'Invalid API response structure: '.$validator->errors()->first(),
                errorCode: ApiErrorCodeEnum::OPERATION_FAILED->value,
                context: ['errors' => $validator->errors()->toArray(), 'data' => $data]
            );
        }

        return new self(
            apilogs: (string) $data['apilogs'],
            dailyreport: (string) $data['dailyreport'],
            debugmode: (string) $data['debugmode'],
            defaultsender: (string) $data['defaultsender'],
            mincreditalarm: (int) $data['mincreditalarm'],
            resendfailed: (string) $data['resendfailed']
        );
    }

    public function hasApiLogsEnabled(): bool
    {
        return $this->apilogs === 'enabled' || $this->apilogs === '1';
    }

    public function hasDailyReportEnabled(): bool
    {
        return $this->dailyreport === 'enabled' || $this->dailyreport === '1';
    }

    public function hasDebugModeEnabled(): bool
    {
        return $this->debugmode === 'enabled' || $this->debugmode === '1';
    }

    public function hasResendFailedEnabled(): bool
    {
        return $this->resendfailed === 'enabled' || $this->resendfailed === '1';
    }
}
