<?php

declare(strict_types=1);

namespace Domain\User\Exceptions;

class UserNotAuthorized extends \DomainException
{
    /**
     * UserNotAuthorized constructor.
     */
    public function __construct()
    {
        parent::__construct('user_not_authorized', 13);
    }
}
