<?php

namespace Navel\Event;

/**
 *
 * @author Julien SAGOT
 */
interface EventDispatcherInterface
{
    /**
     * @return string The module's name
     */
    public function getName();

    /**
     *
     * @param string $type
     * @param callable $callback
     */
    public function addEventListener($type, callable $listener);

    /**
     *
     * @param string $type
     */
    public function removeEventListener($type);

    /**
     *
     * @param Event $event
     */
    public function dispatchEvent(Event $event);

    /**
     *
     * @param string $type
     * @return boolean
     */
    public function hasEventListener($type);

}
