<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Serializer;

use Symfony\Component\EventDispatcher\Event;

/**
 * Serializer for events
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
interface EventSerializer
{
    /**
     * Serialize event
     *
     * @param Event $event
     *
     * @return string
     * @throws UnsupportedObjectException If provided object can not be serialized
     * @throws SerializerException        Any other errors during serialization
     */
    public function serializeEvent(Event $event);

    /**
     * Deserialize event from string
     *
     * @param string $data
     *
     * @return Event
     * @throws UnexpectedFormatException If format of string does not match
     * @throws UnexpectedResultException If deserialized object is not an Event
     * @throws SerializerException       Any other errors during deserialization
     */
    public function deserializeEvent($data);
}
