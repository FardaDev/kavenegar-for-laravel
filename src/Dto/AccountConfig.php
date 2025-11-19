<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Dto;

use FardaDev\Kavenegar\Enums\ApiErrorCodeEnum;
use FardaDev\Kavenegar\Enums\ApiLogsStateEnum;
use FardaDev\Kavenegar\Enums\ConfigStateEnum;
use FardaDev\Kavenegar\Exceptions\KavenegarApiException;
use Illuminate\Support\Facades\Validator;

readonly class AccountConfig
{
    public function __construct(
        public ApiLogsStateEnum $apilogs,
        public ConfigStateEnum $dailyreport,
        public ConfigStateEnum $debugmode,
        public string $defaultsender,
        public int $mincreditalarm,
        public ConfigStateEnum $resendfailed
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

        // Convert string values to enums with proper error handling
        try {
            $apilogs = ApiLogsStateEnum::fromApiValue($data['apilogs']);
            $dailyreport = ConfigStateEnum::fromApiValue($data['dailyreport']);
            $debugmode = ConfigStateEnum::fromApiValue($data['debugmode']);
            $resendfailed = ConfigStateEnum::fromApiValue($data['resendfailed']);
        } catch (\ValueError $e) {
            throw new KavenegarApiException(
                message: "Invalid config state value received from API: {$e->getMessage()}",
                errorCode: ApiErrorCodeEnum::OPERATION_FAILED->value,
                context: ['data' => $data]
            );
        }

        return new self(
            apilogs: $apilogs,
            dailyreport: $dailyreport,
            debugmode: $debugmode,
            defaultsender: (string) $data['defaultsender'],
            mincreditalarm: (int) $data['mincreditalarm'],
            resendfailed: $resendfailed
        );
    }

    public function hasApiLogsEnabled(): bool
    {
        return $this->apilogs->isEnabled();
    }

    public function hasDailyReportEnabled(): bool
    {
        return $this->dailyreport->isEnabled();
    }

    public function hasDebugModeEnabled(): bool
    {
        return $this->debugmode->isEnabled();
    }

    public function hasResendFailedEnabled(): bool
    {
        return $this->resendfailed->isEnabled();
    }
}
