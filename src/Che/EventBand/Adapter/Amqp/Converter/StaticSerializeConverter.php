<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Adapter\Amqp\Converter;

use Che\EventBand\Adapter\Amqp\Converter\MessageEventConverter;
use Che\EventBand\Adapter\Amqp\Driver\AmqpMessage;
use Che\EventBand\Adapter\Amqp\Driver\MessageDelivery;
use Che\EventBand\Adapter\Amqp\Driver\MessagePublication;
use Che\EventBand\Adapter\Amqp\EventConversionException;
use Che\EventBand\Serializer\EventSerializer;
use Che\EventBand\Serializer\SerializerException;
use Symfony\Component\EventDispatcher\Event;

/**
 * Description of StaticSerializeConverter
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class StaticSerializeConverter implements MessageEventConverter
{
    private $serializer;
    private $messageProperties;
    private $routingKey;

    public function __construct(EventSerializer $serializer, array $properties = array(), $routingKey = null)
    {
        $this->serializer = $serializer;
        $this->messageProperties = $properties;
        $this->routingKey = $routingKey;
    }

    /**
     * {@inheritDoc}
     */
    public function createEventPublication(Event $event)
    {
        try {
            return new MessagePublication(
                new AmqpMessage($this->serializer->serializeEvent($event), $this->messageProperties),
                $this->getEventRoutingKey($event),
                false,
                false
            );
        } catch (SerializerException $e) {
            throw new EventConversionException($event, 'Serialize error', $e);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function createDeliveryEvent(MessageDelivery $delivery)
    {
        try {
            return $this->serializer->deserializeEvent($delivery->getMessage()->getBody());
        } catch (SerializerException $e) {
            throw new DeliveryConversionException($delivery, 'Deserialize error', $e);
        }
    }

    protected function getEventRoutingKey(Event $event)
    {
        return $this->routingKey === null ? $event->getName(): $this->routingKey;
    }
}
