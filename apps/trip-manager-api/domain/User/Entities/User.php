<?php

declare(strict_types=1);

namespace Domain\User\Entities;

use Domain\User\Enums\UserType;

class User
{
    /**
     * User constructor.
     *
     * @param ?int $id
     * @param string $name
     * @param string $email
     * @param UserType $type
     */
    public function __construct(
        private ?int $id,
        private string $name,
        private string $email,
        private UserType $type,
    ) {
    }

    /**
     * Gets the user ID.
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the user's name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets the user's email address.
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Gets the user's type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type->value;
    }

    /**
     * Checks if the user can approve orders.
     *
     * @return bool
     */
    public function canApproveOrders(): bool
    {
        return $this->type === UserType::MANAGER;
    }

    /**
     * Checks if the user can manage their own orders.
     *
     * @return bool
     */
    public function canManageOwnOrders(): bool
    {
        return $this->type === UserType::SOLICITOR;
    }
}
