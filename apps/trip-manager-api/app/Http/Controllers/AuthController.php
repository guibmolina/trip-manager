<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Infra\Services\AuthedUserService;
use App\Infra\Services\LoginService;
use Domain\Auth\Exceptions\InvalidCredentialsException;
use Domain\Auth\UseCases\AuthedUser\AuthedUser;
use Domain\Auth\UseCases\Login\Login;
use Domain\Auth\UseCases\Login\LoginDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Handle a login request and return a JWT token if credentials are valid.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $loginDTO = new LoginDTO(
            email: $request->input('email'),
            password: $request->input('password')
        );

        $loginUseCase = new Login(new LoginService());

        try {
            $token = $loginUseCase->execute($loginDTO);
        } catch (InvalidCredentialsException $e) {
            return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Intern error'], 500);
        }

        return response()->json([
            'token' => $token->token
        ]);
    }

    /**
     * Retrieve the authenticated user's information.
     *
     * @return JsonResponse
     */
    public function getUser(): JsonResponse
    {
        $authedUserUseCase = new AuthedUser(new AuthedUserService());
        $user = $authedUserUseCase->execute();
        return response()->json(
            UserResource::make($user)
        );
    }
}
