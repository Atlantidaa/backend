<?php

namespace App\Extensions;

use Illuminate\Http\Response as BaseResponse;

class Response extends BaseResponse
{
    public static function success($message)
    {
        return new BaseResponse([
            'error' => false,
            'message' => $message,
        ]);
    }

    public static function error($message)
    {
        return new BaseResponse([
            'error' => true,
            'message' => $message,
        ]);
    }
}
