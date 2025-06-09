<?php

declare(strict_types=1);

namespace Domain\Destination\Repositories;

use Domain\Destination\Entities\Destination;

interface DestinationRepository
{
    /**
     * @param integer $id
     * @return Destination|null
     */
    public function findById(int $id): ?Destination;

    /**
     * @return Destination[]
     */
    public function findAll(): array;
}
