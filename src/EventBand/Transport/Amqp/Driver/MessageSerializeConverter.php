<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Transport\Amqp\Driver;

use EventBand\Event;
use EventBand\Serializer\EventSerializer;
use EventBand\Serializer\SerializerException;
use EventBand\Transport\Amqp\Driver\AmqpMessage;
use EventBand\Transport\Amqp\Driver\CustomAmqpMessage;

/**
 * Description of StaticSerializeConverter
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class MessageSerializeConverter implements MessageEventConverter
{
    private $serializer;
    private $prototype;

    public function __construct(EventSerializer $serializer, AmqpMessage $prototype = null)
    {
        $this->serializer = $serializer;
        $this->prototype = $prototype;
    }

    /**
     * {@inheritDoc}
     */
    public function eventToMessage(Event $event)
    {
        try {
            $msg = $this->prototype ? CustomAmqpMessage::createCopy($this->prototype) : new CustomAmqpMessage();
            $msg->setBody($this->serializer->serializeEvent($event));

            return $msg;
        } catch (SerializerException $e) {
            throw new EventConversionException($event, 'Serialize error', $e);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function messageToEvent(AmqpMessage $message)
    {
        try {
            return $this->serializer->deserializeEvent($message->getBody());
        } catch (SerializerException $e) {
            throw new MessageConversionException($message, 'Deserialize error', $e);
        }
    }
}
