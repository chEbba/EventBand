<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class BandDispatcherProxy
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class BandDispatcherProxy implements EventDispatcherInterface
{
    private $dispatcher;
    private $eventPrefix;
    private $listeners;

    public function __construct(EventDispatcherInterface $dispatcher, $eventPrefix)
    {
        $this->dispatcher = $dispatcher;
        $this->eventPrefix = $eventPrefix;
        $this->listeners = new \SplObjectStorage();
    }

    /**
     * {@inheritDoc}
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $wrapper = function (Event $event) use ($listener) {
            $eventName = $event->getName();
            // Deprecated since 2.4, this call can be removed after 3.0
            $event->setName($this->unprefixEvent($eventName));
            // Original dispatcher is used to force the default band usage
            // Use the 2.4+ listener signature
            call_user_func($listener, $event, $eventName, $event->getDispatcher());
        };
        $this->listeners->attach($wrapper, $listener);

        $this->dispatcher->addListener($this->prefixEvent($eventName), $wrapper, $priority);
    }

    /**
     * {@inheritDoc}
     */
    public function removeListener($eventName, $listener)
    {
        if ($wrapper = $this->findWrapper($listener)) {
            $listener = $this->listeners->offsetGet($wrapper);
            $this->listeners->detach($wrapper);
        }

        $this->dispatcher->removeListener($this->prefixEvent($eventName), $listener);
    }

    /**
     * {@inheritDoc}
     */
    public function hasListeners($eventName = null)
    {
        return $this->hasListeners($this->prefixEvent($eventName));
    }

    /**
     * {@inheritDoc}
     */
    public function getListeners($eventName = null)
    {
        if ($eventName !== null) {
            return $this->dispatcher->getListeners($this->prefixEvent($eventName));
        }

        $listeners = [];
        foreach ($this->dispatcher->getListeners() as $eventName => $eventListeners) {
            $unprefixed = $this->unprefixEvent($eventName);
            if ($unprefixed !== $eventName) {
                foreach ($eventListeners as &$listener) {
                    if ($this->listeners->contains($listener)) {
                        $listener = $this->listeners->offsetGet($listener);
                    }
                }
                $listeners[$unprefixed] = $eventListeners;
            }
        }

        return $listeners;
    }

    /**
     * {@inheritDoc}
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        // Copy-Pasted from EventDispatcher::addSubscriber() to ensure addListener() call
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (is_string($params)) {
                $this->addListener($eventName, array($subscriber, $params));
            } elseif (is_string($params[0])) {
                $this->addListener($eventName, array($subscriber, $params[0]), isset($params[1]) ? $params[1] : 0);
            } else {
                foreach ($params as $listener) {
                    $this->addListener($eventName, array($subscriber, $listener[0]), isset($listener[1]) ? $listener[1] : 0);
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        // Copy-Pasted from EventDispatcher::removeSubscriber() to ensure removeListener() call
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (is_array($params) && is_array($params[0])) {
                foreach ($params as $listener) {
                    $this->removeListener($eventName, array($subscriber, $listener[0]));
                }
            } else {
                $this->removeListener($eventName, array($subscriber, is_string($params) ? $params : $params[0]));
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function dispatch($eventName, Event $event = null)
    {
        $this->dispatch($this->prefixEvent($eventName), $event);
    }

    /**
     * Add prefix to event
     *
     * @param string $eventName
     *
     * @return string
     */
    private function prefixEvent($eventName)
    {
        return $this->eventPrefix . $eventName;
    }

    /**
     * Remove prefix from event if prefix exists
     *
     * @param string $eventName
     *
     * @return string
     */
    private function unprefixEvent($eventName)
    {
        if (strpos($eventName, $this->eventPrefix) !== 0) {
            return $eventName;
        }

        return substr($eventName, strlen($this->eventPrefix));
    }

    private function findWrapper($listener)
    {
        foreach ($this->listeners as $wrapped) {
            $original = $this->listeners->getInfo();
            if ($original === $listener) {
                return $wrapped;
            }
        }

        return null;
    }
}
