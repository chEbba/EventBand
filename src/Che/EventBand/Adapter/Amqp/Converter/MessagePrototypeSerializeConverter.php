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
use Che\EventBand\Adapter\Amqp\Driver\CustomAmqpMessage;
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
class MessagePrototypeSerializeConverter implements MessageEventConverter
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
