<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Adapter\Amqp\Driver\PeclAmqp;

use Che\EventBand\Adapter\Amqp\Driver\AmqpMessage;
use Che\EventBand\Adapter\Amqp\Driver\CustomAmqpMessage;
use Che\EventBand\Adapter\Amqp\Driver\MessageDelivery;

/**
 * Description of PeclAmqpMessage
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class PeclAmqpMessage extends CustomAmqpMessage
{
    private static $ATTRIBUTE_MAP = array(
        'content_type' => 'contentType',
        'content_encoding' => 'contentEncoding',
        'message_id' => 'messageId',
        'user_id' => 'userId',
        'app_id' => 'appId',
        'priority' => 'priority',
        'timestamp' => 'timestamp',
        'expiration' => 'expiration',
        'type' => 'type',
        'reply_to' => 'replyTo',
    );

    /**
     * @param \AMQPEnvelope $envelope
     * @return PeclAmqpMessage
     */
    public static function fromEnvelope(\AMQPEnvelope $envelope)
    {
        $properties = array();
        foreach (self::getPropertyNames() as $name) {
            $value = $envelope->{'get'.ucfirst($name)}();
            if (!empty($value)) {
                if ($name === 'timestamp' || $name === 'expiration') {
                    $value = new \DateTime('@'.$value);
                }

                $properties[$name] = $value;
            }
        }

        return self::fromProperties($properties);
    }

    public static function createDelivery(\AMQPEnvelope $envelope, $query)
    {
        return new MessageDelivery(
            self::fromEnvelope($envelope),
            $envelope->getDeliveryTag(),
            $envelope->getExchangeName(),
            $query,
            $envelope->getRoutingKey(),
            $envelope->isRedelivery()
        );
    }

    public static function getMessageAttributes(AmqpMessage $message)
    {
        $attributes = array();
        foreach (self::$ATTRIBUTE_MAP as $attribute => $property) {
            $value = $message->{'get'.ucfirst($property)}();
            if ($value !== null) {
                if ($value instanceof \DateTime) {
                    $value = $value->getTimestamp();
                }

                $attributes[$attribute] = $value;
            }
        }

        return $attributes;
    }
}
