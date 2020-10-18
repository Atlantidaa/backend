<?php

namespace App\Extensions;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

class Lastfm
{
    protected GuzzleClient $guzzleClient;
    protected string $token;

    public function __construct()
    {
        $this->guzzleClient = $this->setGuzzleClient();
        $this->token = config('lastfm.token');
    }

    public function getArtistsHints(string $query)
    {
        try {
            $response = $this->guzzleClient->get(config('lastfm.base_uri'), [
                'query' => [
                    'method' => 'artist.search',
                    'artist' => $query,
                    'api_key' => $this->token,
                    'format' => 'json',
                ]
            ]);

            $content = json_decode($response->getBody()->getContents(), true);

            if (empty($content['results']['artistmatches']['artist'])) {
                return [];
            }

            return array_map(
                [$this, 'mutatorResponse'],
                $content['results']['artistmatches']['artist']
            );
        } catch (GuzzleException $e) {
            //
            return [];
        }
    }

    public function getTracksHints(string $query)
    {
        try {
            $response = $this->guzzleClient->get(config('lastfm.base_uri'), [
                'query' => [
                    'method' => 'track.search',
                    'track' => $query,
                    'api_key' => $this->token,
                    'format' => 'json',
                ]
            ]);

            $content = json_decode($response->getBody()->getContents(), true);

            if (empty($content['results']['trackmatches']['track'])) {
                return [];
            }

            return array_map(
                [$this, 'mutatorResponse'],
                $content['results']['trackmatches']['track']
            );
        } catch (GuzzleException $e) {
            //
            return [];
        }
    }

    protected function mutatorResponse(array $item)
    {
        return $item['name'];
    }

    protected function setGuzzleClient()
    {
        return new GuzzleClient();
    }
}
