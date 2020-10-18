<?php

namespace App\Services;

use App\Contracts\Services\LastfmServiceContract;
use App\Extensions\Lastfm;

class LastfmService implements LastfmServiceContract
{
    protected Lastfm $lastfm;

    public function __construct(Lastfm $lastfm)
    {
        $this->lastfm = $lastfm;
    }

    public function hints(string $query): array
    {
        $artists = $this->lastfm->getArtistsHints($query);
        $tracks = $this->lastfm->getTracksHints($query);

        return array_merge($artists, $tracks);
    }
}
