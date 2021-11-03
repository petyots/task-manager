<?php

namespace App\Domain\Server\Http\Controllers;

use App\Interfaces\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ServerController extends Controller
{
    public function matchAll(): JsonResponse
    {
        return $this->respondWithCustomData(
            data: [
                'health' => 100,
            ],
        );
    }
}
