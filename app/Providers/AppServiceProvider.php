<?php

namespace App\Providers;

use App\Contracts\IssueApiInterface;
use App\Services\Issues\IssueApiProxy;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(IssueApiInterface::class, IssueApiProxy::class);
    }

    public function boot(): void
    {
        $appUrl = (string) config('app.url', '');

        if (Str::startsWith($appUrl, 'https://')) {
            URL::forceRootUrl($appUrl);
            URL::forceScheme('https');
        }
    }
}
