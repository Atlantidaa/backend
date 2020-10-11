<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class YoutubeSearchRequest extends FormRequest
{
    public function rules()
    {
        return [
            'query' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'query.required' => 'Required parameter query missing',
        ];
    }
}
