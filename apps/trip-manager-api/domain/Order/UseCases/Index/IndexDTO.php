<?php

declare(strict_types=1);

namespace Domain\Order\UseCases\Index;

use DateTimeImmutable;
use Domain\Order\Enums\OrderStatus;

class IndexDTO
{
    /**
     * IndexDTO constructor.
     *
     * @param int $userAuthorId
     * @param OrderStatus|null $status
     * @param DateTimeImmutable|null $startDate
     * @param DateTimeImmutable|null $endDate
     * @param int|null $destinationId
     */
    public function __construct(
        public int $userAuthorId,
        public ?OrderStatus $status = null,
        public ?DateTimeImmutable $startDate = null,
        public ?DateTimeImmutable $endDate = null,
        public ?int $destinationId = null,
    ) {
    }

    /**
     * Formats the start date
     *
     * @return string|null.
     */
    public function startDateFormat(): ?string
    {
        return $this->startDate?->format('Y-m-d H:i:s');
    }

    /**
     * Formats the end date
     *
     * @return string|null The formatted end date or null if not set.
     */
    public function endDateFormat(): ?string
    {
        return $this->endDate?->format('Y-m-d H:i:s');
    }
}
