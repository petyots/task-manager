<?php

namespace App\Domain\Users\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthTokenResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = $this;

        return [
            'access_token' => $data['access_token'],
            'token_type' => $data['token_type'],
            'expires_in' => (int) $data['expires_in']
        ];
    }
}
