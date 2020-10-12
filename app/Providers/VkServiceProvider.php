<?php

namespace App\Providers;

use App\Contracts\Services\VkServiceContract;
use App\Services\VkService;
use Illuminate\Support\ServiceProvider;

class VkServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(VkServiceContract::class, VkService::class);
    }
}
