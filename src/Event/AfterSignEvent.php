<?php


namespace Hen\Event;


use Symfony\Component\EventDispatcher\Event;

class AfterSignEvent extends Event
{
    const NAME = 'action.afterSign';
}