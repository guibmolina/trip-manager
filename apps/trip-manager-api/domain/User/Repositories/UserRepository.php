<?php

declare(strict_types=1);

namespace Domain\User\Repositories;

use Domain\User\Entities\User;

interface UserRepository
{
    /**
     * @param integer $id
     * @return User|null
     */
    public function findById(int $id): ?User;
}
