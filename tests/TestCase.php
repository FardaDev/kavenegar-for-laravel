<?php declare(strict_types=1);

namespace FardaDev\Kavenegar\Tests;

use FardaDev\Kavenegar\KavenegarServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            KavenegarServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('kavenegar.api_key', 'test-api-key');
        config()->set('kavenegar.sender', '10004346');
        config()->set('kavenegar.skip_in_development', false);
    }
}
