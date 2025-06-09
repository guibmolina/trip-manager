<?php

declare(strict_types=1);

namespace Domain\Order\UseCases\Index;

use Domain\Order\List\OrderList as OrderListAggregate;
use Domain\Order\Repositories\OrderRepository;
use Domain\User\Exceptions\UserNotFoundException;
use Domain\User\Repositories\UserRepository;

class Index
{
    /**
     * Index constructor.
     *
     * @param OrderRepository $orderRepository
     * @param UserRepository $userRepository
     */
    public function __construct(private OrderRepository $orderRepository, private UserRepository $userRepository)
    {
    }

    /**
     * Executes the order listing use case.
     *
     * @param IndexDTO $indexDTO
     * @return OrderListAggregate
     * @throws UserNotFoundException
     */
    public function execute(IndexDTO $indexDTO): OrderListAggregate
    {
        $user = $this->userRepository->findById($indexDTO->userAuthorId);

        if ($user === null) {
            throw new UserNotFoundException();
        }

        return $this->orderRepository->findAll(
            $indexDTO->startDateFormat(),
            $indexDTO->endDateFormat(),
            $indexDTO->status,
            $indexDTO->destinationId,
            $user->canApproveOrders() ? null : $user->getId()
        );
    }
}
