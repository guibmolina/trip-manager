<?php

declare(strict_types=1);

namespace Domain\Order\Exceptions;

class CancelException extends \DomainException
{
    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }
}
