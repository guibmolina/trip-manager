<?php

declare(strict_types=1);

namespace Domain\User\Enums;

enum UserType: string
{
    case SOLICITOR = 'SOLICITOR';
    case MANAGER = 'MANAGER';
}
