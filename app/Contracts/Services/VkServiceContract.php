<?php

namespace App\Contracts\Services;

interface VkServiceContract
{
    public function search(string $query): array;

    public function hints(string $query): array;
}
