<?php

namespace App\Http\Controllers;

use App\Contracts\Services\YoutubeServiceContract;
use App\Http\Requests\YoutubeSearchRequest;
use App\Extensions\Response;

class YoutubeController extends Controller
{
    protected YoutubeServiceContract $youtubeService;

    public function __construct(YoutubeServiceContract $youtubeService)
    {
        $this->youtubeService = $youtubeService;
    }

    public function search(YoutubeSearchRequest $request)
    {
        $result = $this->youtubeService->search($request->get('query'));

        return Response::success($result);
    }

    public function download(string $id)
    {
        $result = $this->youtubeService->download($id);

        return Response::success($result);
    }
}
