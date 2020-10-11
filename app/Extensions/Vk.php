<?php

namespace App\Extensions;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Vodka2\VKAudioToken\AndroidCheckin;
use Vodka2\VKAudioToken\IFAuth;
use Vodka2\VKAudioToken\IFAuthException;
use Vodka2\VKAudioToken\SmallProtobufHelper;
use Vodka2\VKAudioToken\CommonParams;
use Vodka2\VKAudioToken\MTalkClient;
use Vodka2\VKAudioToken\SupportedClients;
use Vodka2\VKAudioToken\TokenReceiverBoom;

class Vk
{
    public string $token = '';

    public function __construct(string $login, string $password)
    {
        $this->authorize($login, $password);
    }

    protected function authorize(string $login, string $password): bool
    {
        $params = new CommonParams(SupportedClients::Boom()->getUserAgent());
        $protobufHelper = new SmallProtobufHelper();

        $checkin = new AndroidCheckin($params, $protobufHelper);
        $authData = $checkin->doCheckin();

        $mtalkClient = new MTalkClient($authData, $protobufHelper);
        $mtalkClient->sendRequest();

        unset($authData['idStr']);

        $ifAuth = new IFAuth($login, $password, $params, SupportedClients::Boom(), "audio,messages,offline");

        try {
            $result = $ifAuth->getTokenAndId();
            $token = $result['token'];
            $userId = $result['userId'];
        } catch (IFAuthException $ex) {
            if ($ex->code == IFAuthException::TWOFA_REQ) {
                echo $ex->extra['message']."\n";
                echo $ex->extra['base64State']."\n"; // pass this and code from sms next time
                exit(1);
            } else {
                throw $ex;
            }
        }
        $receiver = new TokenReceiverBoom($authData, $params);

        list($token) = $receiver->getToken($token, $userId);

        if (!empty($token)) {
            $this->token = $token;

            return true;
        }

        return false;
    }

    public function search(string $query)
    {
        $client = new Client([
            'base_uri' => 'https://api.vk.com',
        ]);

        try {
            $response = $client->get('/method/audio.search', [
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
}
