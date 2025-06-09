<?php

declare(strict_types=1);

namespace Domain\Auth\Services;

use Domain\Auth\UseCases\Login\LoginDTO;
use Domain\User\Entities\User;

interface LoginInterface
{
    /**
     * Authenticates a user .
     *
     * @param LoginDTO $DTO
     * @return string|bool.
     */
    public function login(LoginDTO $DTO): string|bool;
}
