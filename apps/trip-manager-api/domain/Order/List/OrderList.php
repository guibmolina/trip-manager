<?php

declare(strict_types=1);

namespace Domain\Order\List;

use ArrayIterator;
use Domain\Order\Entities\Order;
use IteratorAggregate;
use Traversable;

/** @implements IteratorAggregate<Order> */
class OrderList implements IteratorAggregate
{
    /** @var Order[] */
    private array $orders;

    /**
     * Initializes the OrderList with an empty array of orders.
     */
    public function __construct()
    {
        $this->orders = [];
    }

    /**
     * Adds an Order to the list.
     *
     * @param Order $order The order to add.
     * @return void
     */
    public function add(Order $order): void
    {
        $this->orders[] = $order;
    }

    /** @return array<Order> */
    public function orders(): array
    {
        return $this->orders;
    }

    /** @return Traversable<Order> */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->orders);
    }
}
