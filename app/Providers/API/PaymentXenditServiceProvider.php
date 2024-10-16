<?php

namespace App\Providers\API;

use App\Services\API\PaymentXenditService;
use App\Services\API\Impl\PaymentXenditServiceImpl;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class PaymentXenditServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        PaymentXenditService::class => PaymentXenditServiceImpl::class
    ];

    public function provides()
    {
        return [PaymentXenditService::class];
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
