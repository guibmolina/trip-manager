<?php

declare(strict_types=1);

namespace App\Infra\Repositories;

use App\Models\Destination;
use Domain\Destination\Repositories\DestinationRepository as DestinationRepositoryInterface;
use Domain\Destination\Entities\Destination as DestinationDomain;

/**
 * Repository of Destination.
 */
class DestinationRepository implements DestinationRepositoryInterface
{

    /**
     * Find a destination by its ID.
     *
     * @param int $destinationId
     * @return DestinationDomain|null
     */
    public function findById(int $destinationId): ?DestinationDomain
    {
        $destination = Destination::find($destinationId);

        if (!$destination) {
            return null;
        }
        return $this->mapToDomain($destination);
    }

    /**
     * Maps a Destination Eloquent model to a Destination domain entity.
     *
     * @param Destination $destination
     * @return DestinationDomain
     */
    private function mapToDomain(Destination $destination): DestinationDomain
    {
        return new DestinationDomain(
            $destination->id,
            $destination->city,
            $destination->iata_code,
            $destination->country
        );
    }

    /**
     * Retrieve all destinations as an array.
     *
     * @return array
     */
    public function findAll(): array
    {
        return Destination::all()->toArray();
    }
}
