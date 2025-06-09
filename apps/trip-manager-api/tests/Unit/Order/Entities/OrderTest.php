<?php

use Domain\Order\Entities\Order;
use Domain\Order\Enums\OrderStatus;
use Domain\Order\Exceptions\ApproveException;
use Domain\Order\Exceptions\CancelException;
use Domain\Order\Exceptions\InvalidDatesException;
use Domain\User\Entities\User;
use Domain\Destination\Entities\Destination;

beforeEach(function () {
    $this->user = mock(User::class);
    $this->destination = mock(Destination::class);
    $this->departureDate = new DateTimeImmutable('2024-08-15');
    $this->returnDate = new DateTimeImmutable('2024-08-22');
});

it('creates a valid order', function () {
    $order = new Order(
        id: 1,
        user: $this->user,
        destination: $this->destination,
        departureDate: $this->departureDate,
        returnDate: $this->returnDate,
    );

    expect($order->getId())->toBe(1);
    expect($order->getUser())->toBe($this->user);
    expect($order->getDestination())->toBe($this->destination);
    expect($order->getDepartureDate())->toBe($this->departureDate);
    expect($order->getReturnDate())->toBe($this->returnDate);
    expect($order->getStatus())->toBe(OrderStatus::REQUESTED->value);
    expect($order->getApprovedAt())->toBeNull();
});

it('throws InvalidDatesException when departureDate is after returnDate in constructor', function () {
    $this->expectException(InvalidDatesException::class);

    new Order(
        id: 1,
        user: $this->user,
        destination: $this->destination,
        departureDate: new DateTimeImmutable('2024-08-22'),
        returnDate: new DateTimeImmutable('2024-08-15'),
    );
});

it('throws InvalidDatesException when departureDate is after returnDate in setDepartureDate', function () {
    $order = new Order(
        id: 1,
        user: $this->user,
        destination: $this->destination,
        departureDate: $this->departureDate,
        returnDate: $this->returnDate,
    );

    $this->expectException(InvalidDatesException::class);

    $order->setDepartureDate(new DateTimeImmutable('2024-08-23'));
});

it('throws InvalidDatesException when returnDate is before departureDate in setReturnDate', function () {
    $order = new Order(
        id: 1,
        user: $this->user,
        destination: $this->destination,
        departureDate: $this->departureDate,
        returnDate: $this->returnDate,
    );

    $this->expectException(InvalidDatesException::class);

    $order->setReturnDate(new DateTimeImmutable('2024-08-14'));
});

it('sets departureDate and returnDate successfully', function () {
    $order = new Order(
        id: 1,
        user: $this->user,
        destination: $this->destination,
        departureDate: $this->departureDate,
        returnDate: $this->returnDate,
    );

    $newDepartureDate = new DateTimeImmutable('2024-08-16');
    $newReturnDate = new DateTimeImmutable('2024-08-24');

    $order->setDepartureDate($newDepartureDate);
    $order->setReturnDate($newReturnDate);

    expect($order->getDepartureDate())->toBe($newDepartureDate);
    expect($order->getReturnDate())->toBe($newReturnDate);
});

it('sets destination successfully', function () {
    $order = new Order(
        id: 1,
        user: $this->user,
        destination: $this->destination,
        departureDate: $this->departureDate,
        returnDate: $this->returnDate,
    );

    $newDestination = Mockery::mock(Destination::class);
    $order->setDestination($newDestination);

    expect($order->getDestination())->toBe($newDestination);
});

it('approves an order successfully', function () {
    $order = new Order(
        id: 1,
        user: $this->user,
        destination: $this->destination,
        departureDate: $this->departureDate,
        returnDate: $this->returnDate,
    );

    $approvedAt = new DateTimeImmutable('2024-08-10');
    $order->approve($approvedAt);

    expect($order->getStatus())->toBe(OrderStatus::APPROVED->value);
    expect($order->getApprovedAt())->toBe($approvedAt);
});

it('throws ApproveException if order status is not requested', function () {
    $order = new Order(
        id: 1,
        user: $this->user,
        destination: $this->destination,
        departureDate: $this->departureDate,
        returnDate: $this->returnDate,
        status: OrderStatus::APPROVED,
    );

    $approvedAt = new DateTimeImmutable('2024-08-10');

    $this->expectException(ApproveException::class);

    $order->approve($approvedAt);
});

it('throws ApproveException if departureDate is before approvedAt', function () {
    $order = new Order(
        id: 1,
        user: $this->user,
        destination: $this->destination,
        departureDate: $this->departureDate,
        returnDate: $this->returnDate,
    );

    $approvedAt = new DateTimeImmutable('2024-08-20');

    $this->expectException(ApproveException::class);

    $order->approve($approvedAt);
});

it('cancels a requested order successfully', function () {
    $order = new Order(
        id: 1,
        user: $this->user,
        destination: $this->destination,
        departureDate: $this->departureDate,
        returnDate: $this->returnDate,
    );

    $order->cancel();

    expect($order->getStatus())->toBe(OrderStatus::CANCELED->value);
});

it('throws CancelException if order is already canceled', function () {
    $order = new Order(
        id: 1,
        user: $this->user,
        destination: $this->destination,
        departureDate: $this->departureDate,
        returnDate: $this->returnDate,
        status: OrderStatus::CANCELED,
    );

    $this->expectException(CancelException::class);

    $order->cancel();
});

it('cancels an approved order successfully', function () {
    $approvedAt = new DateTimeImmutable();

    $departureDate = $approvedAt->add(new DateInterval('P7D'));
    $returnDate = $departureDate->add(new DateInterval('P7D'));

        $order = Order::fromState(
            id: 1,
            user: $this->user,
            destination: $this->destination,
            returnDate: $returnDate,
            departureDate: $departureDate,
            approvedAt: $approvedAt,
            status: OrderStatus::APPROVED
        );

    $order->cancel();

    expect($order->getStatus())->toBe(OrderStatus::CANCELED->value);
});

it('throws CancelException if approved order is canceled after 24 hours', function () {
    $now = new DateTimeImmutable();
    $approvedAt = $now->sub(new DateInterval('P2D'));
    $departureDate = $approvedAt->add(new DateInterval('P7D'));
    $returnDate = $departureDate->add(new DateInterval('P7D'));

        $order = Order::fromState(
            id: 1,
            user: $this->user,
            destination: $this->destination,
            returnDate: $returnDate,
            departureDate: $departureDate,
            approvedAt: $approvedAt,
            status: OrderStatus::APPROVED
        );

    $this->expectException(CancelException::class);

    $order->cancel();
});

it('throws CancelException if approved order is canceled less than 7 days before departure', function () {
    $approvedAt = new DateTimeImmutable();
    $departureDate = $approvedAt->add(new DateInterval('P1D'));
    $returnDate = $departureDate->add(new DateInterval('P7D'));

        $order = Order::fromState(
            id: 1,
            user: $this->user,
            destination: $this->destination,
            returnDate: $returnDate,
            departureDate: $departureDate,
            approvedAt: $approvedAt,
            status: OrderStatus::APPROVED
        );

    $this->expectException(CancelException::class);

    $order->cancel();
});

it('creates an order from state successfully', function () {
    $id = 1;
    $user = mock(User::class);
    $destination = mock(Destination::class);
    $returnDate = new DateTimeImmutable('2024-08-22');
    $departureDate = new DateTimeImmutable('2024-08-15');
    $approvedAt = new DateTimeImmutable('2024-08-10');
    $status = OrderStatus::APPROVED;

    $order = Order::fromState(
        id: $id,
        user: $user,
        destination: $destination,
        returnDate: $returnDate,
        departureDate: $departureDate,
        approvedAt: $approvedAt,
        status: $status
    );

    expect($order->getId())->toBe($id);
    expect($order->getUser())->toBe($user);
    expect($order->getDestination())->toBe($destination);
    expect($order->getDepartureDate())->toBe($departureDate);
    expect($order->getReturnDate())->toBe($returnDate);
    expect($order->getApprovedAt())->toBe($approvedAt);
    expect($order->getStatus())->toBe($status->value);
});
