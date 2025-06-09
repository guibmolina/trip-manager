<?php

declare(strict_types=1);

namespace Domain\Auth\UseCases\AuthedUser;

use Domain\Auth\Exceptions\UserNotFoundException;
use Domain\Auth\Services\AuthedUserInterface;
use Domain\User\Entities\User;

/**
 * Retrive authenticated user.
 */
class AuthedUser
{
    /**
     * AuthedUser constructor.
     *
     * @param AuthedUserInterface $service
     */
    public function __construct(private AuthedUserInterface $service)
    {
    }

    /**
     * Retrieves the authenticated user.
     *
     * @return User|null
     * @throws UserNotFoundException
     */
    public function execute(): ?User
    {
        $user = $this->service->getUserAuthed();
        if (!$user) {
            throw new UserNotFoundException();
        }
        return $user;
    }
}
