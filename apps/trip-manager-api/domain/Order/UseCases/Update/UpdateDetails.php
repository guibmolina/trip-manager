<?php

declare(strict_types=1);

namespace Domain\Order\UseCases\Update;

use Domain\Destination\Exceptions\DestinationNotFoundException;
use Domain\Destination\Repositories\DestinationRepository;
use Domain\Order\Exceptions\OrderNotFoundException;
use Domain\Order\Repositories\OrderRepository;
use Domain\User\Exceptions\UserNotAuthorized;
use Domain\User\Exceptions\UserNotFoundException;
use Domain\User\Repositories\UserRepository;

class UpdateDetails
{
    /**
     * UpdateDetails constructor.
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
     * Updates the details of an order.
     *
     * @param UpdateDTO $dto
     * @throws OrderNotFoundException
     * @throws UserNotFoundException
     * @throws UserNotAuthorized
     * @throws DestinationNotFoundException
     */
    public function execute(UpdateDTO $dto): void
    {
        $order = $this->orderRepository->findById($dto->orderId);

        if ($order === null) {
            throw new OrderNotFoundException();
        }

        $userAuthor = $this->userRepository->findById($dto->userAuthorId);

        if ($userAuthor === null) {
            throw new UserNotFoundException();
        }

        if ($userAuthor->getId() !== $order->getUserId()) {
            throw new UserNotAuthorized();
        }

        $destination = $this->destinationRepository->findById($dto->destinationId);

        if ($destination === null) {
            throw new DestinationNotFoundException();
        }

        $order->setDepartureDate($dto->departureDate);
        $order->setReturnDate($dto->returnDate);
        $order->setDestination($destination);

        $this->orderRepository->save($order);
    }
}
