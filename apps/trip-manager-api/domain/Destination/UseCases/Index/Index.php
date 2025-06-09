<?php

declare(strict_types=1);

namespace Domain\Destination\UseCases\Index;

use Domain\Destination\Repositories\DestinationRepository;

/**
 * Use case for listing all destinations.
 */
class Index
{
    /**
     * Index constructor.
     *
     * @param DestinationRepository $destinationRepository
     */
    public function __construct(private DestinationRepository $destinationRepository)
    {
    }

    /**
     * Executes the use case 
     *
     * @return array.
     */
    public function execute(): array
    {
        return $this->destinationRepository->findAll();
    }
}