<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Adapter\Amqp\Driver\AmqpLib;

use Che\EventBand\Adapter\Amqp\Driver\AmqpMessage;
use Che\EventBand\Adapter\Amqp\Driver\CustomAmqpMessage;
use Che\EventBand\Adapter\Amqp\Driver\MessageDelivery;
use PhpAmqpLib\Message\AMQPMessage as AmqpLibMessage;

/**
 * Description of AmqpLibCustomMessage
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class AmqpLibCustomMessage extends CustomAmqpMessage
{
    private static $PROPERTY_MAP = array(
        'headers' => 'application_headers',
        'contentType' => 'content_type',
        'contentEncoding' => 'content_encoding',
        'messageId' => 'message_id',
        'appId' => 'app_id',
        'userId' => 'user_id',
        'priority' => 'priority',
        'timestamp' => 'timestamp',
        'expiration' => 'expiration',
        'type' => 'type',
        'replyTo' => 'reply_to'
    );

    /**
     * @param AmqpLibMessage $message
     *
     * @return self
     */
    public static function fromAmqpLibMessage(AmqpLibMessage $message)
    {
        $properties = array('body' => $message->body);
        foreach (self::$PROPERTY_MAP as $name => $amqpLibName) {
            if ($message->has($amqpLibName)) {
                $value = $message->get($amqpLibName);
                if ($name === 'timestamp' || $name === 'expiration') {
                    $value = new \DateTime('@'.$value);
                }
                $properties[$name] = $message->get($amqpLibName);
            }
        }

        return self::fromProperties($properties);
    }

    public static function createAmqpLibMessage(AmqpMessage $message)
    {
        $amqpLibMessage = new AmqpLibMessage($message->getBody());
        foreach (self::$PROPERTY_MAP as $name => $amqpLibName) {
            $value = $message->{'get'.ucfirst($name)}();
            if ($value !== null) {
                if ($value instanceof \DateTime) {
                    $value = $value->getTimestamp();
                }
                $amqpLibMessage->set($amqpLibName, $value);
            }
        }

        return $amqpLibMessage;
    }

    public static function createDelivery(AmqpLibMessage $msg, $queue)
    {
        return new MessageDelivery(
            AmqpLibCustomMessage::fromAmqpLibMessage($msg),
            $msg->delivery_info['delivery_tag'],
            $msg->delivery_info['exchange'],
            $queue,
            $msg->delivery_info['routing_key'],
            $msg->delivery_info['redelivered']
        );
    }
}
