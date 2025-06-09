<?php

declare(strict_types=1);

namespace Domain\Destination\Exceptions;

class DestinationNotFoundException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('destination_not_found', 12);
    }
}
