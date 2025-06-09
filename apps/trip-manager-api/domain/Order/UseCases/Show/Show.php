<?php

declare(strict_types=1);

namespace Domain\Order\UseCases\Show;

use Domain\Order\Entities\Order;
use Domain\Order\Exceptions\OrderNotFoundException;
use Domain\Order\Repositories\OrderRepository;
use Domain\User\Exceptions\UserNotAuthorized;
use Domain\User\Exceptions\UserNotFoundException;
use Domain\User\Repositories\UserRepository;

class Show
{
    /**
     * Show constructor.
     *
     * @param OrderRepository $orderRepository
     * @param UserRepository $userRepository
     */
    public function __construct(private OrderRepository $orderRepository, private UserRepository $userRepository)
    {
    }

    /**
     *
     * @param int $orderId
     * @param int $userAuthorId
     * @return Order
     * @throws OrderNotFoundException
     * @throws UserNotFoundException
     * @throws UserNotAuthorized
     */
    public function execute(int $orderId, int $userAuthorId): Order
    {
        $order = $this->orderRepository->findById($orderId);
        if ($order === null) {
            throw new OrderNotFoundException();
        }
        $userAuthor = $this->userRepository->findById($userAuthorId);
        if ($userAuthor === null) {
            throw new UserNotFoundException();
        }
        if ($userAuthor->getId() !== $order->getUserId() && !$userAuthor->canApproveOrders()) {
            throw new UserNotAuthorized();
        }
        return $order;
    }
}
