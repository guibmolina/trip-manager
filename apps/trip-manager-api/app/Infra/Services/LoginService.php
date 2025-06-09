<?php

declare(strict_types=1);

namespace App\Infra\Services;

use Domain\Auth\Services\LoginInterface;
use Domain\Auth\UseCases\Login\LoginDTO;
use Domain\User\Entities\User;
use Domain\User\Enums\UserType;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Service responsible for handling user login and authentication.
 */
class LoginService implements LoginInterface
{
    /**
     * Attempt to log in a user with the provided credentials.
     *
     * @param LoginDTO $DTO
     * @return string|bool JWT token string on success, false on failure
     */
    public function login(LoginDTO $DTO): string|bool
    {
        $token = JWTAuth::attempt([
            'email' => $DTO->email,
            'password' => $DTO->password,
        ]);

        return $token;
    }

    /**
     * Retrieve the currently authenticated user as a domain User entity.
     *
     * @return User
     */
    public function getAuthUser(): User
    {
        $user = Auth::user();

        return $this->mapUser($user);
    }

    /**
     * Maps an Authenticatable user to a domain User entity.
     *
     * @param Authenticatable $user
     * @return User
     */
    private function mapUser(Authenticatable $user): User
    {
        return new User(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            type: UserType::from($user->type),
        );
    }
}
