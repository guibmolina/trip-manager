<?php

declare(strict_types=1);

namespace Domain\Order\UseCases\Create;

use Domain\Destination\Repositories\DestinationRepository;
use Domain\Destination\Exceptions\DestinationNotFoundException;
use Domain\Order\Entities\Order;
use Domain\Order\Repositories\OrderRepository;
use Domain\User\Exceptions\UserNotFoundException;
use Domain\User\Exceptions\UserNotAuthorized;
use Domain\User\Repositories\UserRepository;

class Create
{
    /**
     * Create constructor.
     *
     * @param OrderRepository $orderRepository
     * @param UserRepository $userRepository
     * @param DestinationRepository $destinationRepository
     */
    public function __construct(
        private OrderRepository $orderRepository,
        private UserRepository $userRepository,
        private DestinationRepository $destinationRepository
    ) {
    }

    /**
     * Executes the order creation process.
     *
     * @param CreateDTO $dto
     * @throws UserNotFoundException
     * @throws UserNotAuthorized
     * @throws DestinationNotFoundException
     */
    public function execute(CreateDTO $dto): void
    {
        $user = $this->userRepository->findById($dto->userId);

        if ($user === null) {
            throw new UserNotFoundException();
        }

        if (!$user->canManageOwnOrders()) {
            throw new UserNotAuthorized();
        }

        $destination = $this->destinationRepository->findById($dto->destinationId);
        if ($destination === null) {
            throw new DestinationNotFoundException();
        }

        $order = new Order(
            id: null,
            user: $user,
            destination: $destination,
            departureDate: $dto->departureDate,
            returnDate: $dto->returnDate
        );

        $this->orderRepository->save($order);
    }
}
