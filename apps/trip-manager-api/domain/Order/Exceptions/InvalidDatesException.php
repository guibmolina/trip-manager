<?php

declare(strict_types=1);

namespace Domain\Order\Exceptions;

class InvalidDatesException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('invalid_date', 10);
    }
}
