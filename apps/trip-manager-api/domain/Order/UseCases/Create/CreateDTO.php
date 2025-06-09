<?php

declare(strict_types=1);

namespace Domain\Order\UseCases\Create;

use DateTimeImmutable;

class CreateDTO
{
    /**
     * CreateDTO constructor.
     *
     * @param int $userId
     * @param int $destinationId
     * @param DateTimeImmutable $departureDate
     * @param DateTimeImmutable $returnDate
     */
    public function __construct(
        public int $userId,
        public int $destinationId,
        public DateTimeImmutable $departureDate,
        public DateTimeImmutable $returnDate,
    ) {
    }
}
