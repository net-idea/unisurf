<?php

declare(strict_types=1);

namespace App\Entity;

/**
 * Stub entity for booking functionality (work in progress).
 * This class is a placeholder until the full booking feature is implemented.
 *
 * @codeCoverageIgnore
 */
class FormBookingEntity
{
    private ?int $id = null;
    private string $email = '';
    private string $name = '';
    private string $confirmationToken = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getConfirmationToken(): string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }
}
