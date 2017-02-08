<?php

namespace spec\Nixilla\CqrsBundle\Services;

use Nixilla\CqrsBundle\Services\HandlerCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class HandlerCollectionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(HandlerCollection::class);
    }

    function it_get_handlers_for_given_event_name()
    {
        $this->getHandler($eventName = 'some name')->shouldReturn(null);
        $this->setHandler($handler = [], $eventName = 'some name')->shouldReturn(null);
        $this->getHandler($eventName = 'some name')->shouldNotReturn(null);
    }
}
