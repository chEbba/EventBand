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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Proxy all methods to internal dispatcher
 * This class is instance of EventDispatcher and can be used in Event
 *
 * @see Event::setDispatcher
 * @see EventDispatcher
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class EventDispatcherProxy extends EventDispatcher  implements EventDispatcherInterface
{
    private $internalDispatcher;

    /**
     * @param EventDispatcherInterface $internalDispatcher
     */
    public function __construct(EventDispatcherInterface $internalDispatcher)
    {
        $this->internalDispatcher = $internalDispatcher;
    }

    /**
     * Get internal event dispatcher
     *
     * @return EventDispatcherInterface
     */
    public function getInternalDispatcher()
    {
        return $this->internalDispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function dispatch($eventName, Event $event = null)
    {
        return $this->internalDispatcher->dispatch($eventName, $event);
    }

    /**
     * {@inheritDoc}
     */
    public function getListeners($eventName = null)
    {
        return $this->internalDispatcher->getListeners($eventName);
    }

    /**
     * {@inheritDoc}
     */
    public function hasListeners($eventName = null)
    {
        return $this->internalDispatcher->hasListeners($eventName);
    }

    /**
     * {@inheritDoc}
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->internalDispatcher->addListener($eventName, $listener, $priority);
    }

    /**
     * {@inheritDoc}
     */
    public function removeListener($eventName, $listener)
    {
        $this->internalDispatcher->removeListener($eventName, $listener);
    }

    /**
     * {@inheritDoc}
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->internalDispatcher->addSubscriber($subscriber);
    }

    /**
     * {@inheritDoc}
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->internalDispatcher->removeSubscriber($subscriber);
    }
}
