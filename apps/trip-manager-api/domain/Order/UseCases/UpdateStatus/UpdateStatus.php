<?php

declare(strict_types=1);

namespace Domain\Order\UseCases\UpdateStatus;

use Domain\Auth\Exceptions\UserNotFoundException;
use Domain\Destination\Repositories\DestinationRepository;
use Domain\Order\Enums\OrderStatus;
use Domain\Order\Exceptions\OrderNotFoundException;
use Domain\Order\Repositories\OrderRepository;
use Domain\Order\Services\OrderService;
use Domain\User\Exceptions\UserNotAuthorized;
use Domain\User\Repositories\UserRepository;

class UpdateStatus
{
    /**
     * UpdateStatus constructor.
     *
     * @param OrderRepository $orderRepository
     * @param UserRepository $userRepository
     * @param DestinationRepository $destinationRepository
     * @param OrderService $orderService
     */
    public function __construct(
        private OrderRepository $orderRepository,
        private UserRepository $userRepository,
        private DestinationRepository $destinationRepository,
        private OrderService $orderService
    ) {
    }

    /**
     *
     * @param UpdateStatusDTO $dto
     *
     * @throws OrderNotFoundException
     * @throws UserNotFoundException
     * @throws UserNotAuthorized
     */
    public function execute(UpdateStatusDTO $dto): void
    {
        $order = $this->orderRepository->findById($dto->orderId);

        if ($order === null) {
            throw new OrderNotFoundException();
        }

        $userAuthor = $this->userRepository->findById($dto->userAuthorId);

        if ($userAuthor === null) {
            throw new UserNotFoundException();
        }

        if (!$userAuthor->canApproveOrders()) {
            throw new UserNotAuthorized();
        }

        match ($dto->status) {
            OrderStatus::APPROVED => $order->approve(new \DateTimeImmutable()),
            OrderStatus::CANCELED => $order->cancel(),
        };

        $this->orderRepository->save($order);

        $this->orderService->sendEmailOrderStatusUpdate($order);
    }
}
