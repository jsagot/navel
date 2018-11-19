<?php

namespace Navel\Event;

/**
 * Description of EventManager
 *
 * @author Julien SAGOT
 */
final class EventManager
{
	/**
	 * [private register]
	 * @var [array]
	 */
    private static $register = [];

	/* DEBUG
	public static function getRegister()
	{
		return self::$register;
	} */

    /**
     *
     * @param string $type
     * @param callable $listener
     * @param EventDispatcherInterface $target
     * @return void
     */
    public static function registerEvent($type, callable $listener, EventDispatcherInterface $target)
    {
        if(!isset(self::$register[$type]))
        {
            self::$register[$type] = [];
        }
        if(!isset(self::$register[$type][$target->getName()]))
        {
            self::$register[$type][$target->getName()] = [];
        }
        if(isset(self::$register[$type][$target->getName()]))
        {
            in_array($listener, self::$register[$type][$target->getName()]) ? null :
                self::$register[$type][$target->getName()][] = $listener;
        }
    }

    /**
     *
     * @param EventDispatcherInterface $target
     * @param string $type
     * @return void
     */
    public static function unregisterEvent($type, EventDispatcherInterface $target)
    {
        unset(self::$register[$type][$target->getName()]);
    }

    /**
     *
     * @param EventDispatcherInterface $target
     * @param string $type
     * @return boolean
     */
    public static function hasRegisteredEvent($type, EventDispatcherInterface $target)
    {
        return isset(self::$register[$type][$target->getName()]);
    }

    /**
     *
     * @param Event $event
     * @return void
     */
    public static function triggerEvent(Event $event)
    {
        $target = $event->getTarget();
        $name = $target->getName();
        $type = $event->getType();
        if(self::hasRegisteredEvent($type, $target))
        {
            foreach (self::$register[$type][$name] as $listener)
            {
                call_user_func($listener, $event);
            }
        }
    }

	private function __construct() { }
}
