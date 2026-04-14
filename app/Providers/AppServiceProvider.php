<?php

namespace App\Providers;

use App\Contracts\IssueApiInterface;
use App\Contracts\SsoServiceInterface;
use App\Services\Auth\LocalSsoService;
use App\Services\Issues\IssueApiProxy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(IssueApiInterface::class, IssueApiProxy::class);
        $this->app->singleton(SsoServiceInterface::class, LocalSsoService::class);
    }

    public function boot(): void
    {
        //
    }
}
