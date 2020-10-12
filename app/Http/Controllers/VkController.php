<?php

namespace App\Http\Controllers;

use App\Contracts\Services\VkServiceContract;
use App\Http\Requests\MusicSearchRequest;
use App\Extensions\Response;

class VkController extends Controller
{
    protected VkServiceContract $vkService;

    public function __construct(VkServiceContract $vkService)
    {
        $this->vkService = $vkService;
    }

    public function search(MusicSearchRequest $request)
    {
        $result = $this->vkService->search($request->get('query'));

        return Response::success($result);
    }
}
