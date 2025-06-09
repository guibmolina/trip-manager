<?php

declare(strict_types=1);

namespace Domain\Auth\Exceptions;

/**
 * Exception thrown when a user is not found in the system.
 */
class UserNotFoundException extends \DomainException
{
    /**
     * UserNotFoundException constructor.
     *
     * @param string $message
     */
    public function __construct(string $message = 'user_not_found', int $code = 02)
    {
        parent::__construct($message, $code);
    }
}
