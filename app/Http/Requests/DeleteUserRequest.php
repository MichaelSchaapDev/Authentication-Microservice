<?php

namespace App\Http\Requests;

class DeleteUserRequest extends ApiRequest
{
    public function authorize()
    {
        $user = $this->user();
        return $user && ($user->role === 'admin' || $user->role === 'manager');
    }

    public function rules()
    {
        return [];
    }

    public function messages()
    {
        return [];
    }
}
