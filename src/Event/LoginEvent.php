<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class LoginEvent extends Event
{
    public const NAME = 'login.event';

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
