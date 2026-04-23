<?php

namespace App\Providers;

use App\Contracts\IssueApiInterface;
use App\Services\Issues\IssueApiProxy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(IssueApiInterface::class, IssueApiProxy::class);
    }

    public function boot(): void
    {
        //
    }
}
