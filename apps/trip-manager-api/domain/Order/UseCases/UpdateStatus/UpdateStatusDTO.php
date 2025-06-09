<?php

declare(strict_types=1);

namespace Domain\Order\UseCases\UpdateStatus;

use Domain\Order\Enums\OrderStatus;

class UpdateStatusDTO
{
    /**
     * UpdateStatusDTO constructor.
     *
     * @param int $orderId
     * @param int $userAuthorId
     * @param OrderStatus $status
     */
    public function __construct(
        public int $orderId,
        public int $userAuthorId,
        public OrderStatus $status
    ) {
    }
}
