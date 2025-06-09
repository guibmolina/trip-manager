<?php

declare(strict_types=1);

namespace Domain\Auth\Exceptions;

/**
 * Exception thrown when invalid credentials are provided during authentication.
 */
class InvalidCredentialsException extends \DomainException
{
    /**
     * InvalidCredentialsException constructor.
     *
     * @param string $message.
     */
    public function __construct(string $message = 'invalid_credentials', int $code = 01)
    {
        parent::__construct($message, $code);
    }
}