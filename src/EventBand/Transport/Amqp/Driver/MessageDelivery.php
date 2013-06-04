<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Transport\Amqp\Driver;

/**
 * Amqp message delivery
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class MessageDelivery implements \Serializable, \JsonSerializable
{
    private $message;
    private $tag;
    private $exchange;
    private $queue;
    private $routingKey;
    private $redelivered;

    public function __construct(AmqpMessage $message, $deliveryTag, $exchange, $queue, $routingKey = '', $redelivered = false)
    {
        $this->message = $message;
        $this->tag = $deliveryTag;
        $this->exchange = $exchange;
        $this->queue = $queue;
        $this->routingKey = $routingKey;
        $this->redelivered = (bool) $redelivered;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function getExchange()
    {
        return $this->exchange;
    }

    public function getQueue()
    {
        return $this->queue;
    }

    public function getRoutingKey()
    {
        return $this->routingKey;
    }

    public function isRedelivered()
    {
        return $this->redelivered;
    }

    /**
     * Get an array serializable with json
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'message'     => CustomAmqpMessage::getMessageProperties($this->message),
            'tag'         => $this->tag,
            'exchange'    => $this->exchange,
            'queue'       => $this->queue,
            'routingKey'  => $this->routingKey,
            'redelivered' => $this->redelivered,
        ];
    }

    /**
     * Serialize publication
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this->jsonSerialize());
    }

    /**
     * Unserialize array data
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        if (!is_array($data)) {
            throw new \UnexpectedValueException('Unserialized data is not an array');
        }

        // TODO: check existence
        $this->message      = CustomAmqpMessage::fromProperties($data['message']);
        $this->tag          = (string) $data['tag'];
        $this->exchange     = (string) $data['exchange'];
        $this->queue        = (string) $data['queue'];
        $this->routingKey   = (string) $data['routingKey'];
        $this->redelivered  = (bool) $data['routingKey'];
    }
}
