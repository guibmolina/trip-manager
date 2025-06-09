<?php

declare(strict_types=1);

namespace Domain\Order\Services;

use Domain\Order\Entities\Order;

interface OrderService
{
    /**
     * Sends an email notification when the order status is updated.
     *
     * @param Order $order
     */
    public function sendEmailOrderStatusUpdate(Order $order): void;
}
