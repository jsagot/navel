<?php

namespace Navel\Event;

/**
 * Description of Event
 *
 * @author Julien SAGOT
 */
class Event
{
	/**
	 * [const EVENT base event]
	 * @var string
	 */
	const EVENT = 'Event.event';

    /**
     *
     * @var string Event type
     */
    private $type;

    /**
     * @var EventDispatcherInterface
     */
    private $target;

    /**
     *
     * @return string Event type
     */
    public function getType()
    {
        return $this->type;
    }

	/**
     *
     * @return EventDispatcherInterface target
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     *
     * @param string $type
     * @param EventDispatcherInterface $target
     */
    public function __construct($type, EventDispatcherInterface $target)
    {
        $this->type = $type;
        $this->target = $target;
    }
}
