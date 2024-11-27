<?php

namespace App\Providers;

use App\Services\PaymentService;
use App\Services\Impl\PaymentServiceImpl;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        PaymentService::class => PaymentServiceImpl::class
    ];

    public function provides()
    {
        return [PaymentService::class];
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
