<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Serializer;

use Che\EventBand\Serializer\EventSerializer;
use Che\EventBand\Serializer\SerializerException;
use Che\EventBand\Serializer\UnexpectedFormatException;
use Che\EventBand\Serializer\UnexpectedResultException;
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
        try {
            return serialize($event);
        } catch (\Exception $e) {
            throw new SerializerException('Exception in serialize(event)', $e);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function deserializeEvent($data)
    {
        $event = @unserialize($data);
        if ($event === false && $data) {
            throw new UnexpectedFormatException('native', 'Can not "unserialize" data');
        }

        if (!$event instanceof Event) {
            throw new UnexpectedResultException($event, 'Event');
        }

        return $event;
    }

}
