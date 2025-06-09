<?php

declare(strict_types=1);

namespace Domain\Destination\Entities;

/**
 * Class Destination Entity
 *
 */
class Destination
{
    /**
     * Destination constructor.
     *
     * @param int|null $id
     * @param string $city
     * @param string $iataCode
     * @param string $country
     */
    public function __construct(
        private ?int $id,
        private string $city,
        private string $iataCode,
        private string $country,
    ) {
    }

    /**
     * Get the destination ID.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the city of the destination.
     *
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * Get the country of the destination.
     *
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * Get the IATA code of the destination.
     *
     * @return string
     */
    public function getIataCode(): string
    {
        return $this->iataCode;
    }
}
