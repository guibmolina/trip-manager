<?php

use Domain\Order\Entities\Order;
use Domain\Order\Exceptions\OrderNotFoundException;
use Domain\Order\Repositories\OrderRepository;
use Domain\Order\UseCases\Show\Show;
use Domain\User\Entities\User;
use Domain\User\Exceptions\UserNotAuthorized;
use Domain\User\Exceptions\UserNotFoundException;
use Domain\User\Repositories\UserRepository;

it('shows an order successfully when user is the order owner', function () {
    $orderRepository = mock(OrderRepository::class);
    $userRepository = mock(UserRepository::class);

    $orderId = 1;
    $userAuthorId = 2;

    $order = mock(Order::class);
    $order->shouldReceive('getUserId')
        ->once()
        ->andReturn($userAuthorId);

    $userAuthor = mock(User::class);
    $userAuthor->shouldReceive('getId')
        ->once()
        ->andReturn($userAuthorId);

    $orderRepository->shouldReceive('findById')
        ->with($orderId)
        ->andReturn($order);

    $userRepository->shouldReceive('findById')
        ->with($userAuthorId)
        ->andReturn($userAuthor);

    $useCase = new Show(
        $orderRepository,
        $userRepository
    );

    $result = $useCase->execute($orderId, $userAuthorId);

    expect($result)->toBe($order);
});

it('shows an order successfully when user can approve orders', function () {
    $orderRepository = mock(OrderRepository::class);
    $userRepository = mock(UserRepository::class);

    $orderId = 1;
    $userAuthorId = 2;
    $orderOwnerId = 3;

    $order = mock(Order::class);
    $order->shouldReceive('getUserId')
        ->once()
        ->andReturn($orderOwnerId);

    $userAuthor = mock(User::class);
    $userAuthor->shouldReceive('getId')
        ->once()
        ->andReturn($userAuthorId);
    $userAuthor->shouldReceive('canApproveOrders')
        ->once()
        ->andReturn(true);

    $orderRepository->shouldReceive('findById')
        ->with($orderId)
        ->andReturn($order);

    $userRepository->shouldReceive('findById')
        ->with($userAuthorId)
        ->andReturn($userAuthor);

    $useCase = new Show(
        $orderRepository,
        $userRepository
    );

    $result = $useCase->execute($orderId, $userAuthorId);

    expect($result)->toBe($order);
});

it('throws OrderNotFoundException when order is not found', function () {
    $orderRepository = mock(OrderRepository::class);
    $userRepository = mock(UserRepository::class);

    $orderId = 1;
    $userAuthorId = 2;

    $orderRepository->shouldReceive('findById')
        ->with($orderId)
        ->andReturn(null);

    $this->expectException(OrderNotFoundException::class);

    $useCase = new Show(
        $orderRepository,
        $userRepository
    );

    $useCase->execute($orderId, $userAuthorId);
});

it('throws UserNotFoundException when user is not found', function () {
    $orderRepository = mock(OrderRepository::class);
    $userRepository = mock(UserRepository::class);

    $orderId = 1;
    $userAuthorId = 2;

    $orderRepository->shouldReceive('findById')
        ->with($orderId)
        ->andReturn(mock(Order::class));

    $userRepository->shouldReceive('findById')
        ->with($userAuthorId)
        ->andReturn(null);

    $this->expectException(UserNotFoundException::class);

    $useCase = new Show(
        $orderRepository,
        $userRepository
    );

    $useCase->execute($orderId, $userAuthorId);
});

it('throws UserNotAuthorized when user is not authorized to view the order', function () {
    $orderRepository = mock(OrderRepository::class);
    $userRepository = mock(UserRepository::class);

    $orderId = 1;
    $userAuthorId = 2;
    $orderOwnerId = 3;

    $order = mock(Order::class);
    $order->shouldReceive('getUserId')
        ->once()
        ->andReturn($orderOwnerId);

    $userAuthor = mock(User::class);
    $userAuthor->shouldReceive('getId')
        ->once()
        ->andReturn($userAuthorId);

    $userAuthor->shouldReceive('canApproveOrders')
        ->once()
        ->andReturn(false);

    $orderRepository->shouldReceive('findById')
        ->with($orderId)
        ->andReturn($order);

    $userRepository->shouldReceive('findById')
        ->with($userAuthorId)
        ->andReturn($userAuthor);

    $this->expectException(UserNotAuthorized::class);

    $useCase = new Show(
        $orderRepository,
        $userRepository
    );

    $useCase->execute($orderId, $userAuthorId);
});
