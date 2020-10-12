<?php

namespace App\Extensions;

use Vodka2\VKAudioToken\AndroidCheckin;
use Vodka2\VKAudioToken\IFAuth;
use Vodka2\VKAudioToken\IFAuthException;
use Vodka2\VKAudioToken\MTalkException;
use Vodka2\VKAudioToken\SmallProtobufHelper;
use Vodka2\VKAudioToken\CommonParams;
use Vodka2\VKAudioToken\MTalkClient;
use Vodka2\VKAudioToken\SupportedClients;
use Vodka2\VKAudioToken\TokenReceiverBoom;
use Clickalicious\Memcached\Client as MemcacheClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

class Vk
{
    const MEMCACHE_TOKEN_KEY = 'vk_token';

    protected string $token = '';
    protected GuzzleClient $guzzleClient;
    protected MemcacheClient $memcacheClient;

    public function __construct()
    {
        $this->guzzleClient = $this->setGuzzleClient();
        $this->memcacheClient = $this->setMemcacheClient();

        $this->token = $this->getToken();
    }

    protected function getToken()
    {
        $memcachedToken = $this->memcacheClient->get(static::MEMCACHE_TOKEN_KEY);

        if (empty($memcachedToken)) {
            $token = $this->authorize(config('vk.login'), config('vk.password'));
            $this->memcacheClient->set(static::MEMCACHE_TOKEN_KEY, $token);

            return $token;
        }

        return $memcachedToken;
    }

    protected function authorize(string $login, string $password): string
    {
        $params = new CommonParams(SupportedClients::Boom()->getUserAgent());
        $protobufHelper = new SmallProtobufHelper();

        $checkin = new AndroidCheckin($params, $protobufHelper);
        $authData = $checkin->doCheckin();

        $mtalkClient = new MTalkClient($authData, $protobufHelper);
        try {
            $mtalkClient->sendRequest();
        } catch (MTalkException $e) {
            //
        }

        unset($authData['idStr']);

        $ifAuth = new IFAuth($login, $password, $params, SupportedClients::Boom(), "audio,messages,offline");

        try {
            $result = $ifAuth->getTokenAndId();
            $receiver = new TokenReceiverBoom($authData, $params);

            list($token) = $receiver->getToken($result['token'], $result['userId']);

            return $token;
        } catch (IFAuthException $ex) {
            return '';
        }
    }

    public function search(string $query)
    {
        try {
            $response = $this->guzzleClient->get(config('vk.routes.search'), [
                'headers' => [
                    'User-Agent' => SupportedClients::Boom()->getUserAgent(),
                ],
                'query' => [
                    'access_token' => $this->token,
                    'q' => $query,
                    'count' => 20,
                    'v' => '5.95',
                ],
            ]);

            $content = json_decode($response->getBody()->getContents(), true);
            return array_map(
                [$this, 'formatSearchResponse'],
                $content['response']['items']
            );
        } catch (GuzzleException $e) {
            return $e->getMessage();
        }
    }

    protected function formatSearchResponse(array $item)
    {
        return [
            'artist' => $item['artist'],
            'title' => $item['title'],
            'date' => $item['date'],
            'url' => $item['url'],
        ];
    }

    protected function setGuzzleClient()
    {
        return new GuzzleClient([
            'base_uri' => config('vk.base_uri'),
        ]);
    }

    protected function setMemcacheClient()
    {
        return new MemcacheClient(
            config('cache.stores.memcached.servers.0.host'),
            config('cache.stores.memcached.servers.0.port')
        );
    }
}
