<?php

namespace spec\Nixilla\CqrsBundle\Services;

use Nixilla\CqrsBundle\Services\ListenerCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ListenerCollectionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ListenerCollection::class);
    }

    function it_acts_as_a_container_for_projector_objects()
    {
        $event = 'some event';
        $this->getListeners($event)->shouldHaveCount(0);
        $this->addListener(new \stdClass(), $event)->shouldReturn(null);
        $this->getListeners($event)->shouldHaveCount(1);
    }
}
