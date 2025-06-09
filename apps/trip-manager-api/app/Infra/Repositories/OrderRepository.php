<?php

declare(strict_types=1);

namespace App\Infra\Repositories;

use App\Models\Order;
use DateTimeImmutable;
use Domain\Destination\Entities\Destination;
use Domain\Order\Entities\Order as OrderDomain;
use Domain\Order\Enums\OrderStatus;
use Domain\Order\List\OrderList;
use Domain\Order\Repositories\OrderRepository as OrderRepositoryInterface;
use Domain\User\Entities\User;

/**
 * Repository of Order.
 */
class OrderRepository implements OrderRepositoryInterface
{
    /**
     * Save an Order domain entity to the database.
     *
     * @param OrderDomain $orderDomain
     * @return void
     */
    public function save(OrderDomain $orderDomain): void
    {
        Order::updateOrCreate(
            ['id' => $orderDomain->getId()],
            [
                'user_id' => $orderDomain->getUserId(),
                'destination_id' => $orderDomain->getDestinationId(),
                'departure_date' => $orderDomain->getDepartureDate()->format('Y-m-d'),
                'return_date' => $orderDomain->getReturnDate()->format('Y-m-d'),
                'status' => $orderDomain->getStatus(),
                'approved_at' => $orderDomain->getApprovedAt() ??  null,
            ]
        );
    }

    /**
     * Find an Order domain entity by its ID.
     *
     * @param int $id 
     * @return OrderDomain|null
     */
    public function findById(int $id): ?OrderDomain
    {
        $order = Order::with(['user', 'destination'])->find($id);
        if ($order === null) {
            return null;
        }

        return $this->mapToDomain($order);
    }

    /**
     * Find all Order domain entities matching the given filters.
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @param OrderStatus|null $status
     * @param int|null $destinationId
     * @param int|null $userId
     * @return OrderList
     */
    public function findAll(?string $startDate, ?string $endDate, ?OrderStatus $status, ?int $destinationId, ?int $userId): OrderList
    {
        $query = Order::with(['user', 'destination']);
        $query->when($startDate, function ($query, $startDate) {
            $query->where(function ($query) use ($startDate) {
                $query->where('departure_date', '>=', $startDate)
                    ->orWhere('return_date', '>=', $startDate);
            });
        });

        $query->when($endDate, function ($query, $endDate) {
            $query->where(function ($query) use ($endDate) {
                $query->where('return_date', '<=', $endDate)
                    ->orWhere('departure_date', '<=', $endDate);
            });
        });

        $query->when($status, function ($query, $status) {
            $query->where('status', $status->value);
        });

        $query->when($destinationId, function ($query, $destinationId) {
            $query->where('destination_id', $destinationId);
        });

        $query->when($userId, function ($query, $userId) {
            $query->where('user_id', $userId);
        });

        $orders = $query->orderBy('id')->get();

        $orderList = new OrderList();
        foreach ($orders as $order) {
            $orderList->add($this->mapToDomain($order));
        }

        return $orderList;
    }

    /**
     * Maps an Eloquent Order model to a domain Order entity.
     *
     * @param Order $order
     * @return OrderDomain
     */
    private function mapToDomain(Order $order): OrderDomain
    {
        $userDomain = new User(
            id: $order->user->id,
            name: $order->user->name,
            email: $order->user->email,
            type: $order->user->type,
        );

        $destinationDomain = new Destination(
            id: $order->destination->id,
            city: $order->destination->city,
            iataCode: $order->destination->iata_code,
            country: $order->destination->country,
        );

        return OrderDomain::fromState(
            id: $order->id,
            user: $userDomain,
            destination: $destinationDomain,
            departureDate: DateTimeImmutable::createFromMutable($order->departure_date),
            returnDate: DateTimeImmutable::createFromMutable($order->return_date),
            status: $order->status,
            approvedAt: $order->approved_at ? DateTimeImmutable::createFromMutable($order->approved_at) : null,
        );
    }
}
