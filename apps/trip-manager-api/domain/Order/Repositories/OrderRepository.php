<?php

declare(strict_types=1);

namespace Domain\Order\Repositories;

use Domain\Order\Entities\Order;
use Domain\Order\Enums\OrderStatus;
use Domain\Order\List\OrderList;

interface OrderRepository
{
    /**
     * Saves Order entity.
     *
     * @param Order $order
     * @return void
     */
    public function save(Order $order): void;

    /**
     * Finds an Order by  ID.
     *
     * @param int $id
     * @return Order|null
     */
    public function findById(int $id): ?Order;

    /**
     * Finds all orders with filters.
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @param OrderStatus|null $status
     * @param int|null $destinationId
     * @param int|null $userId
     * @return OrderList
     */
    public function findAll(?string $startDate, ?string $endDate, ?OrderStatus $status, ?int $destinationId, ?int $userId): OrderList;
}
