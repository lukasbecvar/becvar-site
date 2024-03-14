<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class LoginEvent
 * 
 * The LoginEvent class represents an event triggered when a user logs in.
 * 
 * @package App\Event
 */
class LoginEvent extends Event
{
    /**
     * The name of the event.
     */
    public const NAME = 'login.event';

    /**
     * @var string|null The username of the user who logged in.
     */
    protected ?string $username;

    /**
     * Constructs a new LoginEvent instance.
     *
     * @param string|null $username The username of the user who logged in.
     */
    public function __construct(?string $username)
    {
        $this->username = $username;
    }

    /**
     * Gets the username of the user who logged in.
     *
     * @return string|null The username of the user who logged in.
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }
}
