<?php

declare(strict_types=1);

namespace Domain\Auth\UseCases\Login;

/**
 * Data Transfer Object for user login credentials.
 */
class LoginDTO
{
    /**
     * LoginDTO constructor.
     *
     * @param string $email    The user's email address.
     * @param string $password The user's password.
     */
    public function __construct(
        public readonly string $email,
        public readonly string $password
    ) {
    }
}
