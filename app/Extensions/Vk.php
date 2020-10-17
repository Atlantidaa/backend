<?php

namespace App\Extensions;

use Vodka2\VKAudioToken\AndroidCheckin;
use Vodka2\VKAudioToken\IFAuth;
use Vodka2\VKAudioToken\IFAuthException;
use Vodka2\VKAudioToken\SmallProtobufHelper;
use Vodka2\VKAudioToken\CommonParams;
use Vodka2\VKAudioToken\SupportedClients;
use Vodka2\VKAudioToken\TokenReceiverBoom;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

use Illuminate\Support\Facades\Cache;

class Vk
{
    const CACHE_TOKEN_KEY = 'vk_token';
    const CACHE_TOKEN_LIFETIME = 86400;

    protected string $token = '';
    protected GuzzleClient $guzzleClient;

    public function __construct()
    {
        $this->guzzleClient = $this->setGuzzleClient();

        try {
            $this->token = $this->getToken();
        } catch (IFAuthException $e) {
            $this->token = '';
        }
    }

    /**
     * Метод получения токена из memcache или при авторизации
     * @return array|bool|float|mixed|string
     * @throws IFAuthException
     */
    protected function getToken()
    {
        $memcachedToken = Cache::get(static::CACHE_TOKEN_KEY);

        if (empty($memcachedToken)) {
            $token = $this->authorize(config('vk.login'), config('vk.password'));
            Cache::put(
                static::CACHE_TOKEN_KEY,
                $token,
                static::CACHE_TOKEN_LIFETIME
            );

            return $token;
        }

        return $memcachedToken;
    }

    /**
     * Метод авторизации во вконтакте (без 2FA)
     * @param string $login
     * @param string $password
     * @return string
     * @throws IFAuthException
     */
    protected function authorize(string $login, string $password): string
    {
        $params = new CommonParams(SupportedClients::Boom()->getUserAgent());
        $protobufHelper = new SmallProtobufHelper();

        $checkin = new AndroidCheckin($params, $protobufHelper);
        $authData = $checkin->doCheckin();

        $ifAuth = new IFAuth($login, $password, $params, SupportedClients::Boom(), "audio,messages,offline");

        $result = $ifAuth->getTokenAndId();
        $receiver = new TokenReceiverBoom($authData, $params);

        list($token) = $receiver->getToken($result['token'], $result['userId']);

        return $token;
    }

    /**
     * Метод поиска по глобальной библиотеке ВК
     * @param string $query
     * @return array|string
     */
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
            return [];
        }
    }

    /**
     * Получение подсказок от вк
     * @param string $query
     * @return array
     */
    public function hints(string $query)
    {
        try {
            $response = $this->guzzleClient->post(config('vk.routes.hints'), [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:81.0) Gecko/20100101 Firefox/81.0',
                    'Authorization' => 'Basic Xzo2YTI2N2YxN2I4Y2EyM2E1NTBiNTA2N2I3MWZmZmU2MzcyMzAzOWE0MGQwNjBiYzMxNzE1ZDZjYzc1NzcxM2NjYjA1YjhmZDc2Y2QzOTFkZTk2Y2I0',
                    'X-Requested-With' => 'XMLHttpRequest',
                    'Host' => 'vk.com',
                    'Origin' => 'https://vk.com',
                ],
                'multipart' => [
                    [
                        'name' => 'act',
                        'contents' => 'a_gsearch_hints',
                    ],
                    [
                        'name' => 'al',
                        'contents' => '1',
                    ],
                    [
                        'name' => 'al_ad',
                        'contents' => '0',
                    ],
                    [
                        'name' => 'q',
                        'contents' => $query,
                    ],
                    [
                        'name' => 'section',
                        'contents' => 'audio',
                    ],
                ],
            ]);

            $content = json_decode($response->getBody()->getContents(), true);
            return array_map(
                [$this, 'formatHintsResponse'],
                $content['payload'][1][0]
            );
        } catch (GuzzleException $e) {
            return [];
        }
    }

    /**
     * Преобразовывает response от сервера ВК
     * @param array $item
     * @return array
     */
    protected function formatSearchResponse(array $item)
    {
        return [
            'artist' => $item['artist'],
            'title' => $item['title'],
            'date' => $item['date'],
            'url' => $item['url'],
            'duration' => $item['duration']
        ];
    }

    /**
     * Преобразование подсказок от вк
     * @param array $item
     * @return mixed
     */
    protected function formatHintsResponse(array $item)
    {
        return $item[3];
    }

    /**
     * Устанавлиеваем guzzle-клиент для дальнейших запросов
     * @return GuzzleClient
     */
    protected function setGuzzleClient()
    {
        return new GuzzleClient([
            'base_uri' => config('vk.base_uri'),
        ]);
    }
}
