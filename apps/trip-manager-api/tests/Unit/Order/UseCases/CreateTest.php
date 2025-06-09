<?php

use Domain\Destination\Exceptions\DestinationNotFoundException;
use Domain\Order\Entities\Order;
use Domain\Order\Repositories\OrderRepository;
use Domain\Order\UseCases\Create\Create;
use Domain\Order\UseCases\Create\CreateDTO;
use Domain\User\Entities\User;
use Domain\User\Exceptions\UserNotFoundException;
use Domain\User\Exceptions\UserNotAuthorized;
use Domain\User\Repositories\UserRepository;
use Domain\Destination\Repositories\DestinationRepository;
use Domain\Destination\Entities\Destination;

it('creates an order successfully', function () {
    $orderRepository = Mockery::mock(OrderRepository::class);
    $userRepository = Mockery::mock(UserRepository::class);
    $destinationRepository = Mockery::mock(DestinationRepository::class);

    $userId = 123;
    $destinationId = 456;
    $departureDate = new DateTimeImmutable('2024-07-15');
    $returnDate = new DateTimeImmutable('2024-07-22');

    $dto = new CreateDTO(
        userId: $userId,
        destinationId: $destinationId,
        departureDate: $departureDate,
        returnDate: $returnDate
    );

    $user = Mockery::mock(User::class);
    $user->shouldReceive('getId')->andReturn($userId);
    $user->shouldReceive('canManageOwnOrders')->andReturn(true);

    $destination = Mockery::mock(Destination::class);

    $userRepository->shouldReceive('findById')
        ->with($userId)
        ->andReturn($user);

    $destinationRepository->shouldReceive('findById')
        ->with($destinationId)
        ->andReturn($destination);

    $orderRepository->shouldReceive('save')
        ->once()
        ->with(Mockery::type(Order::class));


    $createUseCase = new Create(
        $orderRepository,
        $userRepository,
        $destinationRepository
    );

    $createUseCase->execute($dto);
});

it('throws UserNotFoundException when user is not found', function () {
    $orderRepository = Mockery::mock(OrderRepository::class);
    $userRepository = Mockery::mock(UserRepository::class);
    $destinationRepository = Mockery::mock(DestinationRepository::class);

    $userId = 123;
    $destinationId = 456;
    $departureDate = new DateTimeImmutable('2024-07-15');
    $returnDate = new DateTimeImmutable('2024-07-22');

    $dto = new CreateDTO(
        userId: $userId,
        destinationId: $destinationId,
        departureDate: $departureDate,
        returnDate: $returnDate
    );

    $userRepository->shouldReceive('findById')
        ->with($userId)
        ->andReturn(null);

    $this->expectException(UserNotFoundException::class);

    $createUseCase = new Create(
        $orderRepository,
        $userRepository,
        $destinationRepository
    );

    $createUseCase->execute($dto);
});

it('throws UserNotAuthorized when user cannot manage own orders', function () {
    $orderRepository = Mockery::mock(OrderRepository::class);
    $userRepository = Mockery::mock(UserRepository::class);
    $destinationRepository = Mockery::mock(DestinationRepository::class);

    $userId = 123;
    $destinationId = 456;
    $departureDate = new DateTimeImmutable('2024-07-15');
    $returnDate = new DateTimeImmutable('2024-07-22');

    $dto = new CreateDTO(
        userId: $userId,
        destinationId: $destinationId,
        departureDate: $departureDate,
        returnDate: $returnDate
    );

    $user = Mockery::mock(User::class);
    $user->shouldReceive('getId')->andReturn($userId);
    $user->shouldReceive('canManageOwnOrders')->andReturn(false);

    $userRepository->shouldReceive('findById')
        ->with($userId)
        ->andReturn($user);

    $this->expectException(UserNotAuthorized::class);

    $createUseCase = new Create(
        $orderRepository,
        $userRepository,
        $destinationRepository
    );

    $createUseCase->execute($dto);
});

it('throws DestinationNotFoundException when destination is not found', function () {
    $orderRepository = Mockery::mock(OrderRepository::class);
    $userRepository = Mockery::mock(UserRepository::class);
    $destinationRepository = Mockery::mock(DestinationRepository::class);

    $userId = 123;
    $destinationId = 456;
    $departureDate = new DateTimeImmutable('2024-07-15');
    $returnDate = new DateTimeImmutable('2024-07-22');

    $dto = new CreateDTO(
        userId: $userId,
        destinationId: $destinationId,
        departureDate: $departureDate,
        returnDate: $returnDate
    );

    $user = Mockery::mock(User::class);
    $user->shouldReceive('getId')->andReturn($userId);
    $user->shouldReceive('canManageOwnOrders')->andReturn(true);

    $userRepository->shouldReceive('findById')
        ->with($userId)
        ->andReturn($user);

    $destinationRepository->shouldReceive('findById')
        ->with($destinationId)
        ->andReturn(null);

    $this->expectException(DestinationNotFoundException::class);

    $createUseCase = new Create(
        $orderRepository,
        $userRepository,
        $destinationRepository
    );

    $createUseCase->execute($dto);
});
