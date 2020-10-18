<?php

namespace App\Providers;

use App\Contracts\Services\LastfmServiceContract;
use App\Services\LastfmService;
use Illuminate\Support\ServiceProvider;

class LastfmServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(LastfmServiceContract::class, LastfmService::class);
    }
}
