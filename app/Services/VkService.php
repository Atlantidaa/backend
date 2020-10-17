<?php

namespace App\Services;

use App\Contracts\Services\VkServiceContract;
use App\Extensions\Vk;

class VkService implements VkServiceContract
{
    protected Vk $vk;

    public function __construct(Vk $vk)
    {
        $this->vk = $vk;
    }

    public function search(string $query): array
    {
        return $this->vk->search($query);
    }

    public function hints(string $query): array
    {
        return $this->vk->hints($query);
    }
}
