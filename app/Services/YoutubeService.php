<?php

namespace App\Services;

use App\Contracts\Services\YoutubeServiceContract;
use App\Extensions\Youtube;

class YoutubeService implements YoutubeServiceContract
{
    protected Youtube $youtube;

    public function __construct(Youtube $youtube)
    {
        $this->youtube = $youtube;
    }

    public function search(string $query): array
    {
        return $this->youtube->searchVideos($query);
    }

    public function download(string $id): array
    {
        return $this->youtube->getAudioLink($id);
    }
}
