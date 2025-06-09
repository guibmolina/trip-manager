<?php

declare(strict_types=1);

namespace Domain\Order\Exceptions;

class OrderNotFoundException extends \DomainException
{
    /**
     * Constructs a new OrderNotFoundException.
     */
    public function __construct()
    {
        parent::__construct('order_not_found', 11);
    }
}
