<?php

namespace App\Infra\Repositories;

use App\Models\User;
use Domain\User\Entities\User as UserDomain;
use Domain\User\Repositories\UserRepository as UserRepositoryInterface;

/**
 * Repository User.
 */
class UserRepository implements UserRepositoryInterface
{
    /**
     * Find a user by their ID.
     *
     * @param int $userId
     * @return UserDomain|null
     */
    public function findById(int $userId): ?UserDomain
    {
        $user = User::find($userId);
        if (!$user) {
            return null;
        }
        return $this->mapToDomain($user);
    }

    /**
     * Maps an Eloquent User model to a domain User entity.
     *
     * @param User $user
     * @return UserDomain
     */
    private function mapToDomain(User $user): UserDomain
    {
        return new UserDomain(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            type: $user->type
        );
    }
}
