<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Processor;

use EventBand\Event;

/**
 * Class DispatchStopEvent
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class DispatchStopEvent extends StoppableDispatchEvent
{
    private $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function getEvent()
    {
        return $this->event;
    }
}