<?php

namespace spec\Nixilla\CqrsBundle\Services;

use Nixilla\CqrsBundle\Services\ProjectorCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ProjectorCollectionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProjectorCollection::class);
    }

    function it_acts_as_a_container_for_projector_objects()
    {
        $event = 'some event';
        $this->getProjectors($event)->shouldHaveCount(0);
        $this->addProjector(new \stdClass(), $event)->shouldReturn(null);
        $this->getProjectors($event)->shouldHaveCount(1);
    }
}
