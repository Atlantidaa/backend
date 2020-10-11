<?php

namespace App\Contracts\Services;

interface YoutubeServiceContract
{
    public function search(string $query): array;

    public function download(string $id): array;
}
