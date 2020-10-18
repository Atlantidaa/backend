<?php

namespace App\Contracts\Services;

interface LastfmServiceContract
{
    public function hints(string $query): array;
}
