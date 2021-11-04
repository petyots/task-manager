<?php

namespace App\Domain\Users\Http\Controllers;

use App\Domain\Users\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use App\Interfaces\Http\Controllers\Controller;

class ProfilesController extends Controller
{
    public function __construct()
    {
        $this->resourceItem = UserResource::class;
    }

    public function me(): UserResource|JsonResponse
    {
        try {
            return $this->respondWithItem(auth()->user());
        } catch (\Throwable $exception) {
            report($exception);

            return $this->respondWithError(exception: $exception);
        }
    }
}
