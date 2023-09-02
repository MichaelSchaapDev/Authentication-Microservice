<?php

namespace App\Http\Requests;

class RefreshTokenRequest extends ApiRequest
{
    public function rules()
    {
        return [
            'refresh_token' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'refresh_token.required' => 'The refresh_token field is required.',
        ];
    }
}
