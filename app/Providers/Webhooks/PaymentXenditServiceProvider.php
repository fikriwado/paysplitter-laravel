<?php

namespace App\Providers\Webhooks;

use App\Services\PaymentXenditService;
use App\Services\Impl\PaymentXenditServiceImpl;
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
