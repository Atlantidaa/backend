<?php

namespace App\Http\Controllers;

use App\Contracts\Services\LastfmServiceContract;
use App\Http\Requests\MusicSearchRequest;
use App\Extensions\Response;

class LastfmController extends Controller
{
    protected LastfmServiceContract $lastfmService;

    public function __construct(LastfmServiceContract $lastfmService)
    {
        $this->lastfmService = $lastfmService;
    }

    public function hints(MusicSearchRequest $request)
    {
        $result = $this->lastfmService->hints($request->get('query'));

        return Response::success($result);
    }
}
