<?php


namespace Hen\Event;

use Symfony\Component\EventDispatcher\Event;


class BootstrapEvent extends Event
{
    const NAME = 'app.bootstrap';
}