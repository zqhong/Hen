<?php


namespace Hen\Event;


use Symfony\Component\EventDispatcher\Event;

class BeforeSignEvent extends Event
{
    const NAME = 'action.beforeSign';
}