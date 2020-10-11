<?php

namespace App\Http\Controllers;

use App\Http\Requests\MusicSearchRequest;
use Illuminate\Http\Request;
use App\Extensions\Vk;
use App\Extensions\Response;

class VkController extends Controller
{
    public function search(MusicSearchRequest $request)
    {
        $vk = new Vk(config('vk.login'), config('vk.password'));

        return Response::success($vk->search($request->get('query')));
    }
}
