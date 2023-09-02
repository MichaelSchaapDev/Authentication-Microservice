<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserDataResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (string) $this->id,
            'type' => 'users',
            'attributes' => [
                'id' => (string) $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->getRole(),
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at
            ],
        ];
    }
}
