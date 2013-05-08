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
 * Description of MessageDelivery
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class MessageDelivery
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
}
