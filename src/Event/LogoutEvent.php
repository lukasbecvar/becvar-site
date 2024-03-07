<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class LogoutEvent extends Event
{
    public const NAME = 'logout.event';

    protected $username;

    public function __construct($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }
}
