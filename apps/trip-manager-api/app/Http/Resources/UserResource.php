<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'    => $this->getId(),
            'name'  => $this->getName(),
            'email' => $this->getEmail(),
            'type'  => $this->getType(),
        ];
    }
}
