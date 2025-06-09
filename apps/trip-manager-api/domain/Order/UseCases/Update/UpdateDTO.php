<?php

declare(strict_types=1);

namespace Domain\Order\UseCases\Update;

use DateTimeImmutable;

class UpdateDTO
{
    /**
     * UpdateDTO constructor.
     *
     * @param int $orderId
     * @param int $userAuthorId
     * @param int $destinationId
     * @param DateTimeImmutable $departureDate
     * @param DateTimeImmutable $returnDate
     */
    public function __construct(
        public int $orderId,
        public int $userAuthorId,
        public int $destinationId,
        public DateTimeImmutable $departureDate,
        public DateTimeImmutable $returnDate,
    ) {
    }
}
