<?php
declare(strict_types=1);

namespace App\Infra\Services;

use Domain\Auth\Services\AuthedUserInterface;
use Domain\User\Entities\User;

/**
 * Service responsible for retrieving the authenticated user.
 */
class AuthedUserService implements AuthedUserInterface
{
    /**
     * Retrieve the currently authenticated user.
     *
     * @return User|null The authenticated user or null if not authenticated.
     */
    public function getUserAuthed(): ?User
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        return new User(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            type: $user->type,
        );
    }
}
