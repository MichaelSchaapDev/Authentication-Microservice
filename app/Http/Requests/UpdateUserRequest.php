<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateUserRequest extends ApiRequest
{
    public function authorize()
    {
        $user = $this->user();
        return $user && ($user->role === 'admin' || $user->role === 'manager');
    }

    public function rules()
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users')->ignore($this->id)],
            'role' => ['sometimes', 'string', Rule::in(['operator', 'admin'])],
        ];
    }

    public function messages()
    {
        return [];
    }
}
