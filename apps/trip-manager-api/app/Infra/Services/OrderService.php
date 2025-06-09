<?php

namespace App\Infra\Services;

use App\Mail\OrderStatusUpdatedEmail;
use Domain\Order\Entities\Order;
use Domain\Order\Services\OrderService as OrderServiceInterFace;
use Illuminate\Support\Facades\Mail;

/**
 * Service class responsible for handling order-related operations.
 */
class OrderService implements OrderServiceInterFace
{

    /**
     * Send an email notification when the order status is updated.
     *
     * @param Order $order.
     * @return void
     */
    public function sendEmailOrderStatusUpdate(Order $order): void
    {
        Mail::to($order->getUser()->getEmail())->sendNow(new OrderStatusUpdatedEmail($order));
    }
}
