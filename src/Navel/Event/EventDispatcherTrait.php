<?php

namespace Navel\Event;

/**
 * Description of DispatcherTrait
 *
 * @author Julien SAGOT
 */
trait EventDispatcherTrait
{
    /**
     *
     * @var string The module's name
     */
    protected $name;

    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @param type $type
     * @param callable $listener
     */
    public function addEventListener($type, callable $listener)
    {
        EventManager::registerEvent($type, $listener, $this);
    }

    /**
     *
     * @param string $type
     */
    public function removeEventListener($type)
    {
        if($this->hasEventListener($type))
        {
            EventManager::unregisterEvent($type, $this);
        }
    }

    /**
     *
     * @param event $event
     */
    public function dispatchEvent(Event $event)
    {
        EventManager::triggerEvent($event);
    }

    /**
     *
     * @param string $type
     */
    public function hasEventListener($type)
    {
        return EventManager::hasRegisteredEvent($type, $this);
    }

	public function __construct()
	{
		$this->name = uniqid('EventDispatcher');
	}
}
