<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Serializer\Jms;

use Che\EventBand\Serializer\EventSerializer;
use Che\EventBand\Serializer\SerializerException;
use Che\EventBand\Serializer\UnexpectedResultException;
use JMS\Serializer\Exception\UnsupportedFormatException;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event serializer implementation with jms/serializer
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class JmsEventWrapperSerializer implements EventSerializer
{
    private $serializer;
    private $format;

    /**
     * @param SerializerInterface $serializer Serializer instance
     * @param string              $format     Serializer format
     */
    public function __construct(SerializerInterface $serializer, $format)
    {
        $this->serializer = $serializer;
        $this->format = $format;
    }

    /**
     * {@inheritDoc}
     */
    public function serializeEvent(Event $event)
    {
        try {
            return $this->serializer->serialize(new EventWrapper($event), $this->format);
        } catch (\Exception $e) {
            // Can not fully detect an error type, so just throw a generic exception
            throw new SerializerException('Error while serializing event', $e);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function deserializeEvent($data)
    {
        try {
            // EventHandlerWrapper returns pure event object instead of wrapper
            /** @var $event Event */
            $event = $this->serializer->deserialize($data, __NAMESPACE__ . '\EventWrapper', $this->format);
        } catch (UnsupportedFormatException $e) {
            throw new SerializerException(sprintf('Wrong serializer format "%s". Check your configuration', $this->format), $e);
        } catch (\Exception $e) {
            throw new SerializerException(sprintf('Exception in JMS deserialize: %s', $e->getMessage()), $e);
        }

        if (!$event instanceof Event) {
            throw new UnexpectedResultException($event, 'Event');
        }

        return $event;
    }
}
