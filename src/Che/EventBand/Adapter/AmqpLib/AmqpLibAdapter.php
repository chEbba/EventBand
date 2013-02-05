<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Adapter\AmqpLib;

use Che\EventBand\Serializer\EventSerializer;

/**
 * Base class for php-amqplib adapters
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
abstract class AmqpLibAdapter
{
    private $connectionFactory;
    /**
     * @var \PhpAmqpLib\Connection\AMQPConnection
     */
    private $connection;
    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel
     */
    private $channel;
    private $channelId;
    private $serializer;

    /**
     * @param AmqpConnectionFactory $connectionFactory Connection factory instance
     * @param EventSerializer       $serializer        Event serializer instance
     * @param int                   $channelId         AMQP channel id. If 0 new channel will be used.
     *                                                 Otherwise new|existing channel with id will be used and
     *                                                 channel will not be closed on destruct
     */
    public function __construct(AmqpConnectionFactory $connectionFactory, EventSerializer $serializer, $channelId = 0)
    {
        $this->connectionFactory = $connectionFactory;
        $this->serializer = $serializer;
        $this->channelId = $channelId ? (int) $channelId : null; // Convert 0 to null because 0 is reserved for connection itself
    }

    /**
     * Get connection
     *
     * @return \PhpAmqpLib\Connection\AMQPConnection
     */
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->connectionFactory->getConnection();
        }

        return $this->connection;
    }

    /**
     * Get channel
     *
     * @return \PhpAmqpLib\Channel\AMQPChannel
     */
    protected function getChannel()
    {
        if (!$this->channel) {
            $this->channel = $this->getConnection()->channel($this->channelId);
        }

        return $this->channel;
    }

    /**
     * Get event serializer
     *
     * @return EventSerializer
     */
    protected function getSerializer()
    {
        return $this->serializer;
    }

    protected function closeChannel($created = true)
    {
        if (!$this->channel) {
            return false;
        }

        $this->channel = null;
        if (!$created || $this->channelId) {
            $this->channel->close();
        }

        return true;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->closeChannel();
    }
}
