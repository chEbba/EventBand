<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Transport;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class EventContainer
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class EventContainer
{
    private $name;
    private $event;

    public function __construct($name, Event $event)
    {
        $this->name = $name;
        $this->event = $event;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get stored event data
     *
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }
}
