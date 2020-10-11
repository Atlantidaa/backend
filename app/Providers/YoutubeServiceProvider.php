<?php

namespace App\Providers;

use App\Contracts\Services\YoutubeServiceContract;
use App\Services\YoutubeService;
use Illuminate\Support\ServiceProvider;

class YoutubeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(YoutubeServiceContract::class, YoutubeService::class);
    }
}
