<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class LogoutEvent
 * 
 * The LogoutEvent class represents an event triggered when a user logs out.
 * 
 * @package App\Event
 */
class LogoutEvent extends Event
{
    /**
     * The name of the event.
     */
    public const NAME = 'logout.event';

    /**
     * @var string|null The username of the user who logged out.
     */
    protected ?string $username;

    /**
     * Constructs a new LogoutEvent instance.
     *
     * @param string|null $username The username of the user who logged out.
     */
    public function __construct($username)
    {
        $this->username = $username;
    }

    /**
     * Gets the username of the user who logged out.
     *
     * @return string|null The username of the user who logged out.
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }
}
