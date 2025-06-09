<?php

declare(strict_types=1);

namespace Domain\User\Exceptions;

class UserNotFoundException extends \DomainException
{
    /**
     * UserNotFoundException constructor.
     */
    public function __construct()
    {
        parent::__construct('user_not_found', 14);
    }
}
