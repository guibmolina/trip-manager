<?php

declare(strict_types=1);

namespace Domain\Auth\UseCases\Login;

class LoginOutputDTO
{
    public function __construct(
        public readonly string $token,
    ) {
    }
}
