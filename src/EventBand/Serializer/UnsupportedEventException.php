<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Serializer;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event can not be serialized
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class UnsupportedEventException extends SerializerException
{
    private $event;

    /**
     * Constructor
     *
     * @param Event           $event   Serialized object
     * @param string          $reason   Error reason
     * @param \Exception|null $previous Previous exception
     */
    public function __construct(Event $event, $reason, \Exception $previous = null)
    {
        $this->event = $event;

        parent::__construct(
            sprintf('Event "%s" is unsupported by serializer: %s', get_class($event), $reason),
            $previous
        );
    }

    /**
     * Get serialized event
     *
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }
}
