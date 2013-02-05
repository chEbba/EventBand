<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Processor based on EventDispatcher
 * Executes listeners on event
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class DispatcherProcessor
{
    private $dispatcher;
    private $prefix;
    private $dispatcherForEvent;

    /**
     * @param EventDispatcherInterface $dispatcher An event dispatcher instance
     * @param string                   $prefix     If prefix is not empty $prefix$eventName listeners will be dispatched
     *
     * @see Event::setDispatcher
     * @see EventDispatcherProxy
     */
    public function __construct(EventDispatcherInterface $dispatcher, $prefix = '')
    {
        $this->dispatcher = $dispatcher;
        $this->prefix = (string) $prefix;
    }

    /**
     * Get event dispatcher
     *
     * @return EventDispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * Executes event listeners from dispatcher
     *
     * @param Event $event
     */
    public function __invoke(Event $event)
    {
        $eventName = $this->prefix . $event->getName();

        $event->setDispatcher($this->getDispatcherForEvent());

        $listeners = $this->dispatcher->getListeners($eventName);
        foreach ($listeners as $listener) {
            call_user_func($listener, $event);
            if ($event->isPropagationStopped()) {
                break;
            }
        }
    }

    /**
     * Get dispatcher which instance of EventDispatcher and can be set to event
     *
     * @return EventDispatcher
     */
    protected function getDispatcherForEvent()
    {
        if (!$this->dispatcherForEvent) {
            $this->dispatcherForEvent = $this->dispatcher instanceof EventDispatcher ?
                $this->dispatcher :
                new EventDispatcherProxy($this->dispatcher);
        }

        return $this->dispatcherForEvent;
    }
}
