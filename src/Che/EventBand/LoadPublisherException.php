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
 * Exception while loading event publisher
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class LoadPublisherException extends LoadException
{
    private $event;

    /**
     * Constructor
     *
     * @param Event           $event    Event object
     * @param string          $reason   Exception reason
     * @param \Exception|null $previous Previous exception
     */
    public function __construct(Event $event, $reason, \Exception $previous = null)
    {
        $this->event = $event;

        parent::__construct(sprintf('Can not load publisher for event "%s": %s', $event->getName(), $reason), 0, $previous);
    }

    /**
     * Get event object
     *
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }
}
