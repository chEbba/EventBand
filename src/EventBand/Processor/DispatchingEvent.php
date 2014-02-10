<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Processor;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class DispatchingEvent
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
abstract class DispatchingEvent extends Event
{
    private $eventName;
    private $event;

    public function __construct($eventName, Event $event)
    {
        $this->eventName = $eventName;
        $this->event = $event;
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }
} 
