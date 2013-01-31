<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Adapter\AmqpLib;

use Che\EventBand\PublishEventException;
use Che\EventBand\EventPublisher;
use Che\EventBand\Serializer\EventSerializer;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event publisher based on php-amqplib
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class AmqpLibPublisher extends AmqpLibAdapter implements EventPublisher
{
    /** Non-persistent message delivery mode */
    const DELIVERY_MODE_NON_PERSISTENT = 1;
    /** Persistent message delivery mode */
    const DELIVERY_MODE_PERSISTENT = 2;

    private $exchange;
    private $routingKey;
    private $deliveryMode;

    /**
     * Constructor
     *
     * @param AmqpConnectionFactory $connectionFactory Connection factory instance
     * @param EventSerializer       $serializer        Event serializer instance
     * @param string                $exchange          Name of exchange to publish.
     *                                                 Exchange will not be created.
     *                                                 You should setup exchange by yourself
     * @param int                   $channelId         AMQP channel id
     * @param string|null           $routingKey        Message routing key. If null event name will be used.
     *                                                 To pass empty routing key use empty string ''
     * @param int                   $deliveryMode      DELIVERY_MODE_* constant
     *
     * @see AmqpLibAdapter::__construct()
     */
    public function __construct(AmqpConnectionFactory $connectionFactory, EventSerializer $serializer, $exchange,
                                $channelId = 0, $routingKey = null, $deliveryMode = self::DELIVERY_MODE_PERSISTENT)
    {
        $this->exchange =  $exchange;
        $this->routingKey = $routingKey;
        if (!in_array($deliveryMode, array(self::DELIVERY_MODE_PERSISTENT, self::DELIVERY_MODE_NON_PERSISTENT))) {
            throw new \InvalidArgumentException(sprintf('Unknown delivery mode "%s"', $deliveryMode));
        }
        $this->deliveryMode = (int) $deliveryMode;

        parent::__construct($connectionFactory, $serializer, $channelId);
    }

    /**
     * {@inheritDoc}
     */
    public function publishEvent(Event $event)
    {
        try {
            $msg = new AMQPMessage($this->getSerializer()->serializeEvent($event));
            $msg->set('delivery_mode', $this->deliveryMode);
        } catch (\RuntimeException $e) {
            throw new PublishEventException($event, 'Event can not be serialized', $e);
        }

        try {
            $this->getChannel()->basic_publish($msg, $this->exchange, $this->routingKey);
        } catch (\Exception $e) {
            throw new PublishEventException($event, 'AMQP basic_publish error', $e);
        }
    }

    protected function getRoutingKey(Event $event)
    {
        return $this->routingKey === null ? $event->getName(): $this->routingKey;
    }
}
