<?php

namespace App\Application\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiErrorResponseResource extends JsonResource
{
    public function toArray($request): array
    {
        $data = $this;
        $isProduction = config('app.env') === 'production';

        return [
            'status_code' => $data['status_code'],
            'error_code' => $data['error_code'],
            'message' => $data['message'],
            'developer_message' => $this->when(! $isProduction, $data['developer_message']),
            'exception' => $this->when(! $isProduction, $data['exception']),
        ];
    }
}
