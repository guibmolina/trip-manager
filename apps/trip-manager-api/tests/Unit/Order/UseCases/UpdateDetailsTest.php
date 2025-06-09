<?php

use Domain\Destination\Exceptions\DestinationNotFoundException;
use Domain\Order\Entities\Order;
use Domain\Order\Exceptions\OrderNotFoundException;
use Domain\Order\Repositories\OrderRepository;
use Domain\Order\UseCases\Update\UpdateDTO;
use Domain\Order\UseCases\Update\UpdateDetails;
use Domain\User\Entities\User;
use Domain\User\Exceptions\UserNotAuthorized;
use Domain\User\Exceptions\UserNotFoundException;
use Domain\User\Repositories\UserRepository;
use Domain\Destination\Entities\Destination;
use Domain\Destination\Repositories\DestinationRepository;

it('updates order details successfully', function () {

    $orderRepository = mock(OrderRepository::class);
    $userRepository = mock(UserRepository::class);
    $destinationRepository = mock(DestinationRepository::class);

    $orderId = 1;
    $userAuthorId = 2;
    $destinationId = 3;
    $departureDate = new DateTimeImmutable('2024-08-15 00:00:00');
    $returnDate = new DateTimeImmutable('2024-08-22 00:00:00');

    $dto = new UpdateDTO(
        orderId: $orderId,
        userAuthorId: $userAuthorId,
        destinationId: $destinationId,
        departureDate: $departureDate,
        returnDate: $returnDate
    );

    $user = mock(User::class);
    $user->shouldReceive('getId')->andReturn($userAuthorId);

    $destination = mock(Destination::class);

    $order = mock(Order::class);
    $order->shouldReceive('getUserId')->andReturn($userAuthorId);
    $order->shouldReceive('setDepartureDate')->with($departureDate);
    $order->shouldReceive('setReturnDate')->with($returnDate);
    $order->shouldReceive('setDestination')->with($destination);

    $orderRepository->shouldReceive('findById')
        ->with($orderId)
        ->andReturn($order);

    $userRepository->shouldReceive('findById')
        ->with($userAuthorId)
        ->andReturn($user);

    $destinationRepository->shouldReceive('findById')
        ->with($destinationId)
        ->andReturn($destination);

    $orderRepository->shouldReceive('save')
        ->once()
        ->with($order);

    $useCase = new UpdateDetails(
        $orderRepository,
        $userRepository,
        $destinationRepository
    );

    $useCase->execute($dto);

});

it('throws OrderNotFoundException when order is not found', function () {
    $orderRepository = mock(OrderRepository::class);
    $userRepository = mock(UserRepository::class);
    $destinationRepository = mock(DestinationRepository::class);

    $orderId = 1;
    $userAuthorId = 2;
    $destinationId = 3;
    $departureDate = new DateTimeImmutable('2024-08-15 00:00:00');
    $returnDate = new DateTimeImmutable('2024-08-22 00:00:00');

    $dto = new UpdateDTO(
        orderId: $orderId,
        userAuthorId: $userAuthorId,
        destinationId: $destinationId,
        departureDate: $departureDate,
        returnDate: $returnDate
    );

    $orderRepository->shouldReceive('findById')
        ->with($orderId)
        ->andReturn(null);

    $this->expectException(OrderNotFoundException::class);

    $useCase = new UpdateDetails(
        $orderRepository,
        $userRepository,
        $destinationRepository
    );

    $useCase->execute($dto);
});

it('throws UserNotFoundException when user is not found', function () {
    $orderRepository = Mockery::mock(OrderRepository::class);
    $userRepository = Mockery::mock(UserRepository::class);
    $destinationRepository = Mockery::mock(DestinationRepository::class);

    $orderId = 1;
    $userAuthorId = 2;
    $destinationId = 3;
    $departureDate = new DateTimeImmutable('2024-08-15 00:00:00');
    $returnDate = new DateTimeImmutable('2024-08-22 00:00:00');


    $dto = new UpdateDTO(
        orderId: $orderId,
        userAuthorId: $userAuthorId,
        destinationId: $destinationId,
        departureDate: $departureDate,
        returnDate: $returnDate
    );

    $order = mock(Order::class);
    $orderRepository->shouldReceive('findById')
        ->with($orderId)
        ->andReturn($order);

    $userRepository->shouldReceive('findById')
        ->with($userAuthorId)
        ->andReturn(null);

    $this->expectException(UserNotFoundException::class);

    $useCase = new UpdateDetails(
        $orderRepository,
        $userRepository,
        $destinationRepository
    );

    $useCase->execute($dto);
});

it('throws UserNotAuthorized when user is not authorized to update the order', function () {
    $orderRepository = Mockery::mock(OrderRepository::class);
    $userRepository = Mockery::mock(UserRepository::class);
    $destinationRepository = Mockery::mock(DestinationRepository::class);

    $orderId = 1;
    $userAuthorId = 2;
    $destinationId = 3;
    $departureDate = new DateTimeImmutable('2024-08-15 00:00:00');
    $returnDate = new DateTimeImmutable('2024-08-22 00:00:00');


    $dto = new UpdateDTO(
        orderId: $orderId,
        userAuthorId: $userAuthorId,
        destinationId: $destinationId,
        departureDate: $departureDate,
        returnDate: $returnDate
    );

    $user = mock(User::class);
    $user->shouldReceive('getId')->andReturn($userAuthorId);

    $order = mock(Order::class);
    $order->shouldReceive('getUserId')->andReturn(999);
    $orderRepository->shouldReceive('findById')
        ->with($orderId)
        ->andReturn($order);

    $userRepository->shouldReceive('findById')
        ->with($userAuthorId)
        ->andReturn($user);

    $this->expectException(UserNotAuthorized::class);

    $useCase = new UpdateDetails(
        $orderRepository,
        $userRepository,
        $destinationRepository
    );

    $useCase->execute($dto);
});

it('throws DestinationNotFoundException when destination is not found', function () {
    $orderRepository = mock(OrderRepository::class);
    $userRepository = mock(UserRepository::class);
    $destinationRepository = mock(DestinationRepository::class);

    $orderId = 1;
    $userAuthorId = 2;
    $destinationId = 3;
    $departureDate = new DateTimeImmutable('2024-08-15 00:00:00');
    $returnDate = new DateTimeImmutable('2024-08-22 00:00:00');

    $dto = new UpdateDTO(
        orderId: $orderId,
        userAuthorId: $userAuthorId,
        destinationId: $destinationId,
        departureDate: $departureDate,
        returnDate: $returnDate
    );

    $user = mock(User::class);
    $user->shouldReceive('getId')->andReturn($userAuthorId);

    $order = mock(Order::class);
    $order->shouldReceive('getUserId')->andReturn($userAuthorId);

    $orderRepository->shouldReceive('findById')
        ->with($orderId)
        ->andReturn($order);

    $userRepository->shouldReceive('findById')
        ->with($userAuthorId)
        ->andReturn($user);

    $destinationRepository->shouldReceive('findById')
        ->with($destinationId)
        ->andReturn(null);

    $this->expectException(DestinationNotFoundException::class);

    $useCase = new UpdateDetails(
        $orderRepository,
        $userRepository,
        $destinationRepository
    );

    $useCase->execute($dto);
});
