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

/**
 * Simple publisher loader which uses [event -> publisher] map
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class EventPublisherMapLoader implements EventPublisherLoader
{
    private $publishers;
    private $eventPublisherMap;

    /**
     * Constructor with predefined map
     *
     * @param array $publishers An array of name => publisher
     * @param array $events     A map of eventName => publisherName
     *
     * @see setPublisher()
     * @see mapEvent()
     */
    public function __construct(array $publishers = array(), array $events = array())
    {
        $this->publishers = array();
        $this->eventPublisherMap = array();

        foreach ($publishers as $name => $publisher) {
            $this->setPublisher($name, $publisher);
        }

        foreach ($events as $eventName => $publisherName) {
            $this->mapEvent($eventName, $publisherName);
        }
    }

    /**
     * Add named publisher
     *
     * @param string         $name
     * @param EventPublisher $publisher
     *
     * @return $this Provides the fluent interface
     */
    public function setPublisher($name, EventPublisher $publisher)
    {
        $this->publishers[$name] = $publisher;

        return $this;
    }

    /**
     * Get publisher by name
     *
     * @param $name
     *
     * @return EventPublisher
     * @throws \OutOfBoundsException If no publisher with $name exists
     */
    public function getPublisher($name)
    {
        $this->checkPublisher($name);

        return $this->publishers[$name];
    }

    /**
     * Remove publisher with name
     *
     * @param string $name
     *
     * @return EventPublisher Removed publisher
     * @throws \OutOfBoundsException If no publisher with $name exists
     */
    public function removePublisher($name)
    {
        $this->checkPublisher($name);

        $publisher = $this->publishers[$name];
        unset($this->publishers[$name]);

        // Clear connected events
        foreach ($this->eventPublisherMap as $eventName => $publisherName) {
            if ($publisherName === $name) {
                unset($this->eventPublisherMap[$eventName]);
            }
        }

        return $publisher;
    }

    /**
     * Check if publisher with name is registered
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasPublisher($name)
    {
        return isset($this->publishers[$name]);
    }

    /**
     * Associate event with publisher
     *
     * @param string $eventName
     * @param string $publisherName
     *
     * @return $this Provides the fluent interface
     * @throws \OutOfBoundsException If no publisher with $name exists
     */
    public function mapEvent($eventName, $publisherName)
    {
        $this->checkPublisher($publisherName);

        $this->eventPublisherMap[$eventName] = $publisherName;

        return $this;
    }

    /**
     * Remove event association
     *
     * @param string $eventName
     *
     * @return string Associated publisher name
     * @throws \OutOfBoundsException If no event association exists
     */
    public function unmapEvent($eventName)
    {
        $this->checkEventMapped($eventName);

        $publisherName = $this->eventPublisherMap[$eventName];
        unset($this->eventPublisherMap[$eventName]);

        return $publisherName;
    }

    /**
     * Check if event-publisher association exists
     *
     * @param string $eventName
     *
     * @return bool
     */
    public function isEventMapped($eventName)
    {
        return isset($this->eventPublisherMap[$eventName]);
    }

    /**
     * Get publisher mapped to event
     *
     * @param string $eventName
     *
     * @return EventPublisher
     * @throws \OutOfBoundsException If no event association exists
     */
    public function getMappedEventPublisher($eventName)
    {
        $this->checkEventMapped($eventName);

        return $this->eventPublisherMap[$eventName];
    }

    /**
     * Get list of events mapped to publisher
     *
     * @param string $name
     *
     * @return string[]
     * @throws \OutOfBoundsException If no publisher with $name exists
     */
    public function getPublisherEvents($name)
    {
        $this->checkPublisher($name);

        $events = array();
        foreach ($this->eventPublisherMap as $eventName => $publisherName) {
            if ($publisherName === $name) {
                $events[] = $eventName;
            }
        }

        return $events;
    }

    /**
     * {@inheritDoc}
     */
    public function loadPublisherForEvent(Event $event)
    {
        try {
            return $this->getMappedEventPublisher($event->getName());
        } catch (\OutOfBoundsException $e) {
            throw new LoadPublisherException($event, 'Can not find event publisher', $e);
        }
    }

    /**
     * Check that publisher exists
     *
     * @param string $name
     *
     * @throws \OutOfBoundsException
     */
    protected function checkPublisher($name)
    {
        if (!$this->hasPublisher($name)) {
            throw new \OutOfBoundsException(sprintf('Unknown publisher "%s"', $name));
        }
    }

    /**
     * Check that event is mapped
     *
     * @param string $eventName
     *
     * @throws \OutOfBoundsException
     */
    protected function checkEventMapped($eventName)
    {
        if (!$this->isEventMapped($eventName)) {
            throw new \OutOfBoundsException(sprintf('Unknown event "%s"', $eventName));
        }
    }
}
