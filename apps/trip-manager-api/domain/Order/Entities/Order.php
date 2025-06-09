<?php

declare(strict_types=1);

namespace Domain\Order\Entities;

use DateTimeImmutable;
use Domain\Destination\Entities\Destination;
use Domain\Order\Enums\OrderStatus;
use Domain\Order\Exceptions\ApproveException;
use Domain\Order\Exceptions\CancelException;
use Domain\Order\Exceptions\InvalidDatesException;
use Domain\User\Entities\User;

/**
 * Represents an order for a trip.
 */
class Order
{
    private ?DateTimeImmutable $approvedAt = null;
    private DateTimeImmutable $departureDate;
    private DateTimeImmutable $returnDate;

    /**
     * Order constructor.
     *
     * @param ?int $id
     * @param User $user
     * @param Destination $destination
     * @param DateTimeImmutable $returnDate
     * @param DateTimeImmutable $departureDate
     * @param OrderStatus $status
     * @throws InvalidDatesException
     */
    public function __construct(
        private ?int $id,
        private User $user,
        private Destination $destination,
        DateTimeImmutable $returnDate,
        DateTimeImmutable $departureDate,
        private OrderStatus $status = OrderStatus::REQUESTED,
    ) {
        $this->validateDates($departureDate, $returnDate);
        $this->departureDate = $departureDate;
        $this->returnDate = $returnDate;
    }

    /**
     * Gets the order ID.
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the user
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Gets the user ID
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user->getId();
    }

    /**
     * Gets the Destination
     *
     * @return Destination
     */
    public function getDestination(): Destination
    {
        return $this->destination;
    }

    /**
     * Gets the destination ID
     *
     * @return int 
     */
    public function getDestinationId(): int
    {
        return $this->destination->getId();
    }

    /**
     * Gets the departure date.
     *
     * @return DateTimeImmutable
     */
    public function getDepartureDate(): DateTimeImmutable
    {
        return $this->departureDate;
    }

    /**
     * Gets the return date.
     *
     * @return DateTimeImmutable
     */
    public function getReturnDate(): DateTimeImmutable
    {
        return $this->returnDate;
    }

    /**
     * Gets the approval date.
     *
     * @return ?DateTimeImmutable
     */
    public function getApprovedAt(): ?DateTimeImmutable
    {
        return $this->approvedAt;
    }

    /**
     * Gets the order status.
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status->value;
    }

    /**
     * Sets the departure date.
     *
     * @param DateTimeImmutable $departureDate
     * @throws InvalidDatesException
     */
    public function setDepartureDate(DateTimeImmutable $departureDate): void
    {
        $this->validateDates($departureDate, $this->returnDate);
        $this->departureDate = $departureDate;
    }

    /**
     * Sets the return date.
     *
     * @param DateTimeImmutable $returnDate
     * @throws InvalidDatesException
     */
    public function setReturnDate(DateTimeImmutable $returnDate): void
    {
        $this->validateDates($this->departureDate, $returnDate);
        $this->returnDate = $returnDate;
    }

    /**
     * Sets the destination.
     *
     * @param Destination $destination
     */
    public function setDestination(Destination $destination): void
    {
        $this->destination = $destination;
    }

    /**
     * Approves the order.
     *
     * @param DateTimeImmutable $approvedAt
     * @throws ApproveException
     */
    public function approve(DateTimeImmutable $approvedAt): void
    {
        if ($this->status !== OrderStatus::REQUESTED) {
            throw new ApproveException('order_status_not_requested', 17);
        }
        if ($this->departureDate < $approvedAt) {
            throw new ApproveException('invalid_date', 18);
        }

        $this->approvedAt = $approvedAt;
        $this->status = OrderStatus::APPROVED;
    }

    /**
     * Cancels the order.
     *
     * @throws CancelException
     */
    public function cancel(): void
    {
        if ($this->status === OrderStatus::CANCELED) {
            throw new CancelException('order_already_canceled', 15);
        }

        if ($this->status === OrderStatus::APPROVED) {
            if ($this->approvedAt->add(new \DateInterval('P1D')) <= new DateTimeImmutable()) {
                throw new CancelException('order_approved_passed_one_day', 13);
            }

            if (($this->departureDate->diff($this->approvedAt))->days < 7) {
                throw new CancelException('departure_date_and_approved_diff_more_7_days', 14);
            }
        }

        $this->status = OrderStatus::CANCELED;
    }

    /**
     * Validates that the departure date is not after the return date.
     *
     * @param DateTimeImmutable $departureDate
     * @param DateTimeImmutable $returnDate
     * @throws InvalidDatesException
     */
    private function validateDates(DateTimeImmutable $departureDate, DateTimeImmutable $returnDate): void
    {
        if ($departureDate > $returnDate) {
            throw new InvalidDatesException();
        }
    }

    /**
     * Creates an Order object from a given state.
     *
     * @param int $id
     * @param User $user
     * @param Destination $destination
     * @param DateTimeImmutable $returnDate
     * @param DateTimeImmutable $departureDate
     * @param ?DateTimeImmutable $approvedAt
     * @param OrderStatus $status
     * @return self
     */
    public static function fromState(
        int $id,
        User $user,
        Destination $destination,
        DateTimeImmutable $returnDate,
        DateTimeImmutable $departureDate,
        ?DateTimeImmutable $approvedAt,
        OrderStatus $status
    ): self {
        $order = new self(
            id: $id,
            user: $user,
            destination: $destination,
            returnDate: $returnDate,
            departureDate: $departureDate,
            status: $status,
        );
        $order->approvedAt = $approvedAt;

        return $order;
    }
}
