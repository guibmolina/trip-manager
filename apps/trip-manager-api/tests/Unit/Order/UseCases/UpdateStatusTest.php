<?php

use Domain\Order\Entities\Order;
use Domain\Order\Enums\OrderStatus;
use Domain\Order\Exceptions\ApproveException;
use Domain\Order\Exceptions\CancelException;
use Domain\Order\Exceptions\OrderNotFoundException;
use Domain\Order\Repositories\OrderRepository;
use Domain\Order\Services\OrderService;
use Domain\Order\UseCases\UpdateStatus\UpdateStatus;
use Domain\Order\UseCases\UpdateStatus\UpdateStatusDTO;
use Domain\User\Entities\User;
use Domain\User\Exceptions\UserNotAuthorized;
use Domain\User\Repositories\UserRepository;
use Domain\Destination\Repositories\DestinationRepository;

it('updates order status to approved successfully', function () {
    $orderRepository = mock(OrderRepository::class);
    $userRepository = mock(UserRepository::class);
    $destinationRepository = mock(DestinationRepository::class);
    $orderService = mock(OrderService::class);

    $orderId = 1;
    $userAuthorId = 2;

    $dto = new UpdateStatusDTO(
        orderId: $orderId,
        userAuthorId: $userAuthorId,
        status: OrderStatus::APPROVED,
    );

    $order = mock(Order::class);
    $order->shouldReceive('approve')
        ->once();

    $userAuthor = mock(User::class);
    $userAuthor->shouldReceive('canApproveOrders')
        ->once()
        ->andReturn(true);

    $orderRepository->shouldReceive('findById')
        ->with($orderId)
        ->andReturn($order);

    $userRepository->shouldReceive('findById')
        ->with($userAuthorId)
        ->andReturn($userAuthor);

    $orderRepository->shouldReceive('save')
        ->once()
        ->with($order);

    $orderService->shouldReceive('sendEmailOrderStatusUpdate')
        ->once()
        ->with($order);

    $useCase = new UpdateStatus(
        $orderRepository,
        $userRepository,
        $destinationRepository,
        $orderService
    );

    $useCase->execute($dto);

});

it('updates order status to canceled successfully', function () {
    $orderRepository = mock(OrderRepository::class);
    $userRepository = mock(UserRepository::class);
    $destinationRepository = mock(DestinationRepository::class);
    $orderService = mock(OrderService::class);

    $orderId = 1;
    $userAuthorId = 2;

    $dto = new UpdateStatusDTO(
        orderId: $orderId,
        userAuthorId: $userAuthorId,
        status: OrderStatus::CANCELED,
    );

    $order = mock(Order::class);
    $order->shouldReceive('cancel')
        ->once();

    $userAuthor = mock(User::class);
    $userAuthor->shouldReceive('canApproveOrders')
        ->once()
        ->andReturn(true);

    $orderRepository->shouldReceive('findById')
        ->with($orderId)
        ->andReturn($order);

    $userRepository->shouldReceive('findById')
        ->with($userAuthorId)
        ->andReturn($userAuthor);

    $orderRepository->shouldReceive('save')
        ->once()
        ->with($order);

    $orderService->shouldReceive('sendEmailOrderStatusUpdate')
        ->once()
        ->with($order);

    $useCase = new UpdateStatus(
        $orderRepository,
        $userRepository,
        $destinationRepository,
        $orderService
    );

    $useCase->execute($dto);
});

it('throws OrderNotFoundException when order is not found', function () {
    $orderRepository = mock(OrderRepository::class);
    $userRepository = mock(UserRepository::class);
    $destinationRepository = mock(DestinationRepository::class);
    $orderService = mock(OrderService::class);


    $orderId = 1;
    $userAuthorId = 2;

    $dto = new UpdateStatusDTO(
        orderId: $orderId,
        userAuthorId: $userAuthorId,
        status: OrderStatus::APPROVED,
    );

    $orderRepository->shouldReceive('findById')
        ->with($orderId)
        ->andReturn(null);

    $this->expectException(OrderNotFoundException::class);

    $useCase = new UpdateStatus(
        $orderRepository,
        $userRepository,
        $destinationRepository,
        $orderService
    );

    $useCase->execute($dto);
});

it('throws UserNotFoundException when user is not found', function () {
    $orderRepository = mock(OrderRepository::class);
    $userRepository = mock(UserRepository::class);
    $destinationRepository = mock(DestinationRepository::class);
    $orderService = mock(OrderService::class);


    $orderId = 1;
    $userAuthorId = 2;

    $dto = new UpdateStatusDTO(
        orderId: $orderId,
        userAuthorId: $userAuthorId,
        status: OrderStatus::APPROVED,
    );

    $order = mock(Order::class);
    $orderRepository->shouldReceive('findById')
        ->with($orderId)
        ->andReturn($order);

    $userRepository->shouldReceive('findById')
        ->with($userAuthorId)
        ->andReturn(null);

    $this->expectException(\Domain\Auth\Exceptions\UserNotFoundException::class);

    $useCase = new UpdateStatus(
        $orderRepository,
        $userRepository,
        $destinationRepository,
        $orderService
    );

    $useCase->execute($dto);
});

it('throws UserNotAuthorized when user is not authorized to approve orders', function () {
    $orderRepository = mock(OrderRepository::class);
    $userRepository = mock(UserRepository::class);
    $destinationRepository = mock(DestinationRepository::class);
    $orderService = mock(OrderService::class);


    $orderId = 1;
    $userAuthorId = 2;

    $dto = new UpdateStatusDTO(
        orderId: $orderId,
        userAuthorId: $userAuthorId,
        status: OrderStatus::APPROVED,
    );

    $order = mock(Order::class);
    $orderRepository->shouldReceive('findById')
        ->with($orderId)
        ->andReturn($order);

    $userAuthor = mock(User::class);
    $userAuthor->shouldReceive('canApproveOrders')
        ->once()
        ->andReturn(false);

    $userRepository->shouldReceive('findById')
        ->with($userAuthorId)
        ->andReturn($userAuthor);

    $this->expectException(UserNotAuthorized::class);

    $useCase = new UpdateStatus(
        $orderRepository,
        $userRepository,
        $destinationRepository,
        $orderService
    );

    $useCase->execute($dto);
});

it('throws ApproveException when trying to approve an already approved order', function () {
    $orderRepository = mock(OrderRepository::class);
    $userRepository = mock(UserRepository::class);
    $destinationRepository = mock(DestinationRepository::class);
    $orderService = mock(OrderService::class);


    $orderId = 1;
    $userAuthorId = 2;

    $dto = new UpdateStatusDTO(
        orderId: $orderId,
        userAuthorId: $userAuthorId,
        status: OrderStatus::APPROVED,
    );

    $order = mock(Order::class);
    $order->shouldReceive('approve')
        ->once()
        ->andThrow(ApproveException::class);

    $userAuthor = mock(User::class);
    $userAuthor->shouldReceive('canApproveOrders')
        ->once()
        ->andReturn(true);

    $orderRepository->shouldReceive('findById')
        ->with($orderId)
        ->andReturn($order);

    $userRepository->shouldReceive('findById')
        ->with($userAuthorId)
        ->andReturn($userAuthor);

    $this->expectException(ApproveException::class);

    $useCase = new UpdateStatus(
        $orderRepository,
        $userRepository,
        $destinationRepository,
        $orderService
    );

    $useCase->execute($dto);
});

it('throws CancelException when trying to cancel an already canceled order', function () {
    $orderRepository = mock(OrderRepository::class);
    $userRepository = mock(UserRepository::class);
    $destinationRepository = mock(DestinationRepository::class);
    $orderService = mock(OrderService::class);

    $orderId = 1;
    $userAuthorId = 2;

    $dto = new UpdateStatusDTO(
        orderId: $orderId,
        userAuthorId: $userAuthorId,
        status: OrderStatus::CANCELED,
    );

    $order = mock(Order::class);
    $order->shouldReceive('cancel')
        ->once()
        ->andThrow(CancelException::class);

    $userAuthor = mock(User::class);
    $userAuthor->shouldReceive('canApproveOrders')
        ->once()
        ->andReturn(true);

    $orderRepository->shouldReceive('findById')
        ->with($orderId)
        ->andReturn($order);

    $userRepository->shouldReceive('findById')
        ->with($userAuthorId)
        ->andReturn($userAuthor);

    $this->expectException(CancelException::class);

    $useCase = new UpdateStatus(
        $orderRepository,
        $userRepository,
        $destinationRepository,
        $orderService,
    );

    $useCase->execute($dto);
});
