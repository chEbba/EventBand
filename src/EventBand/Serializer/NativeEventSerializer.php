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
 * Serializer based on native PHP serialize
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class NativeEventSerializer implements EventSerializer
{
    /**
     * {@inheritDoc}
     */
    public function serializeEvent(Event $event)
    {
        // Require a custom serialization because of a dispatcher property.
        // Will be able to remove this after Symfony 3.0
        if (!$event instanceof \Serializable) {
            throw new UnsupportedEventException($event, 'Event is not an instance of Serializable');
        }

        try {
            $data = @serialize($event);
            if ($data === 'N;') {// If error , event will be null
                $error = error_get_last();
                throw new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
            }

            return $data;
        } catch (\Exception $e) {
            throw new UnsupportedEventException($event, 'Exception in serialize(event)', $e);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function deserializeEvent($data)
    {
        try {
            $event = @unserialize($data);
        } catch (\Exception $e) {
            throw new SerializerException('Exception in unserialize', $e);
        }

        if ($event === false && $data !== 'b:0;') {// Unserialize generated an error
            $error = error_get_last();
            $previous = new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
            // Treat all errors as format errors
            throw new WrongFormatException('native', 'Can not "unserialize" data', $previous);
        }

        if (!$event instanceof Event) {
            throw new UnexpectedResultException($event, 'Event');
        }

        return $event;
    }
}
