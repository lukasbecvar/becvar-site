<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class RegisterEvent
 * 
 * The RegisterEvent class represents an event triggered when a user registred.
 * 
 * @package App\Event
 */
class RegisterEvent extends Event
{
    /**
     * The name of the event.
     */
    public const NAME = 'register.event';

    /**
     * @var string|null The username of the user who registered.
     */
    protected ?string $username;

    /**
     * Constructs a new RegisterEvent instance.
     *
     * @param string|null $username The username of the user who registered.
     */
    public function __construct(?string $username)
    {
        $this->username = $username;
    }

    /**
     * Gets the username of the user who registered.
     *
     * @return string|null The username of the user who registered.
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }
}
