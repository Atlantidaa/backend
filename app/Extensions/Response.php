<?php

namespace App\Extensions;

use Illuminate\Http\Response as BaseResponse;

class Response extends BaseResponse
{
    public static function success($message)
    {
        return new BaseResponse([
            'errors' => false,
            'message' => $message,
        ]);
    }

    public static function error($message)
    {
        return new BaseResponse([
            'errors' => true,
            'message' => $message,
        ]);
    }
}
