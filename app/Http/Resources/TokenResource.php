<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TokenResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'access_token' => $this->resource["access_token"],
            'refresh_token' => $this->resource["refresh_token"],
        ];
    }
}
