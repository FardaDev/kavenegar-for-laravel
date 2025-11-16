<?php declare(strict_types=1);

namespace FardaDev\Kavenegar;

use FardaDev\Kavenegar\Client\KavenegarClient;
use FardaDev\Kavenegar\Helpers\KavenegarHelper;
use Illuminate\Support\ServiceProvider;

class KavenegarServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/kavenegar.php', 'kavenegar');

        $this->app->singleton(KavenegarClient::class, function ($app) {
            $config = $app['config']['kavenegar'];

            return new KavenegarClient(
                apiKey: $config['api_key'],
                defaultSender: $config['sender'] ?? null,
                timeout: $config['timeout'] ?? 30
            );
        });

        $this->app->alias(KavenegarClient::class, 'kavenegar');

        $this->app->singleton(KavenegarHelper::class, function ($app) {
            return new KavenegarHelper($app->make(KavenegarClient::class));
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/kavenegar.php' => config_path('kavenegar.php'),
            ], 'kavenegar-config');
        }
    }
}
