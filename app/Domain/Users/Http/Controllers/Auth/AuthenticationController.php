<?php

namespace App\Domain\Users\Http\Controllers\Auth;

use App\Domain\Users\Actions\CreateUserAction;
use App\Domain\Users\Events\UserCreatedEvent;
use App\Domain\Users\Factories\UserDataFactory;
use App\Domain\Users\Http\Requests\Auth\LoginRequest;
use App\Domain\Users\Http\Requests\Auth\RegisterRequest;
use App\Domain\Users\Http\Resources\Auth\AuthTokenResponse;
use App\Interfaces\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthenticationController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $data = UserDataFactory::newCreateUserDataFromRegisterRequest($request);
            $user = (new CreateUserAction())->execute($data);

            DB::afterCommit(function () use ($user) {
                event(new UserCreatedEvent($user));
            });

            DB::commit();

            return $this->respondWithCustomData(
                data: ['uuid' => $user->uuid],
                message: __('User created.'),
                status: Response::HTTP_CREATED
            );
        } catch (Throwable $exception) {
            DB::rollBack();

            report($exception);

            return $this->respondWithError(exception: $exception);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (JWTAuth::setRequest($request)->user()) {
            return $this->respondWithError(
                message: __('You are already logged in.'),
                statusCode: Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $credentials = $request->safe(['email', 'password']);

            if (!$token = auth()->attempt($credentials)) {
                return $this->respondWithCustomData(
                    data: [],
                    message: 'Invalid Credentials.',
                    status: Response::HTTP_UNAUTHORIZED,
                );
            }

            AuthTokenResponse::withoutWrapping();
            $data = AuthTokenResponse::make([
                'token_type' => 'bearer',
                'access_token' => $token,
                'expires_in' => JWTAuth::factory()->getTTL() * 60
            ]);

            return response()->json($data);
        } catch (Throwable $exception) {
            report($exception);

            return $this->respondWithError(exception: $exception);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            auth()->logout();

            return $this->respondWithCustomData(
                data: [],
                message: __('You have successfully logged out.')
            );
        } catch (Throwable $exception) {
            report($exception);

            return $this->respondWithError(exception: $exception);
        }
    }

    public function refreshAuthToken(): JsonResponse
    {
        try {
            AuthTokenResponse::withoutWrapping();

            /** @var JWTAuth $guard */
            $guard = auth()->guard();

            [$accessToken, $expiresIn] = [$guard->refresh(), $guard->factory()->getTTL() * 60];

            $data = AuthTokenResponse::make([
                'token_type' => 'bearer',
                'access_token' => $accessToken,
                'expires_in' => $expiresIn
            ]);

            return response()->json($data);
        } catch (\Exception $exception) {
            if ($exception instanceof JWTException) {
                return $this->respondWithError(
                    message: $exception->getMessage(),
                    statusCode: Response::HTTP_UNAUTHORIZED,
                );
            }
            report($exception);

            return $this->respondWithError(exception: $exception);
        }
    }
}
