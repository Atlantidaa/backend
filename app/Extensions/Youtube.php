<?php

namespace App\Extensions;

use Madcoda\Youtube\Facades\Youtube as YoutubeApi;
use YouTube\YouTubeDownloader;

class Youtube
{
    private array $audioItags = [];

    public function __construct()
    {
        $this->audioItags = config('youtube.itags.audio');
    }

    /**
     * Поиск видео по запросу
     * @param string $query
     * @return array
     */
    public function searchVideos(string $query): array
    {
        $youtubeSearchResult = YoutubeApi::searchVideos($query);

        return array_map(
            [$this, 'formatSearchResult'],
            $youtubeSearchResult
        );
    }

    /**
     * Получение ссылок на скачивание аудио-файлов
     * @param string $id
     * @return array|string[]
     */
    public function getAudioLink(string $id): array
    {
        $links = $this->getLinks($id);

        foreach ($links as $link) {
            if (in_array($link['itag'], $this->audioItags)) {
                return [
                    'url' => $link['url'],
                ];
            }
        }

        return ['url' => ''];
    }

    /**
     * Получение всех ссылок на скачивание (все форматы)
     * @param string $id
     * @return array
     */
    protected function getLinks(string $id): array
    {
        $youtube = new YouTubeDownloader();

        return $youtube->getDownloadLinks($id);
    }

    /**
     * Мутатор для response от youtube api
     * @param object $item
     * @return array
     */
    protected function formatSearchResult(object $item): array
    {
        return [
            'id' => $item->id->videoId,
            'title' => $item->snippet->title,
            'publishedAt' => $item->snippet->publishedAt,
            'channelId' => $item->snippet->channelId,
            'thumbnail' => $item->snippet->thumbnails->high->url,
        ];
    }
}
