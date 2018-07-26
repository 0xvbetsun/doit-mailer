<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            $this->mergeWhen($request->route()->getName() === 'api_register', [
                'id' => $this->id,
                'email' => $this->email,
            ]),
            'token' => $this->api_token,
            'avatar' => [
                'main' => $this->getAvatar(),
                'thumbnail' => $this->getAvatarThumbnail()
            ],
        ];
    }
}
