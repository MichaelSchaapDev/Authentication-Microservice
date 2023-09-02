<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserRelationshipResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'user' => [
                'data' => [
                    'id' => (string) $this->id,
                    'type' => 'users',
                ],
            ],
        ];
    }
}
