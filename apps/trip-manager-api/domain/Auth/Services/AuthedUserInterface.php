<?php

declare(strict_types=1);

namespace Domain\Auth\Services;

use Domain\User\Entities\User;

interface AuthedUserInterface
{
    /**
     * Get the authenticated user.
     *
     * @return User|null
     */
    public function getUserAuthed(): ?User;
}