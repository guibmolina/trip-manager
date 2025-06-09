<?php

declare(strict_types=1);

namespace Domain\Auth\UseCases\Login;

use Domain\Auth\Exceptions\InvalidCredentialsException;
use Domain\Auth\Services\LoginInterface;

/**
 * Handles the user login use case.
 */
class Login
{
    /**
     * Login constructor.
     *
     * @param LoginInterface $service.
     */
    public function __construct(private LoginInterface $service)
    {
    }

    /**
     * Executes the login process with the provided credentials.
     *
     * @param LoginDTO $DTO The data transfer object .
     * @return LoginOutputDTO The output data transfer object.
     * @throws InvalidCredentialsException If the credentials are invalid.
     */
    public function execute(LoginDTO $DTO): LoginOutputDTO
    {
        $token = $this->service->login($DTO);

        if (!$token) {
            throw new InvalidCredentialsException();
        }
        return new LoginOutputDTO(
            token: $token,
        );
    }
}
