<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Adapter\Amqp\Driver\PeclAmqp;

use Che\EventBand\Adapter\Amqp\Definition\ConnectionDefinition;
use Che\EventBand\Adapter\Amqp\Driver\AmqpDriver;
use Che\EventBand\Adapter\Amqp\Driver\AmqpMessage;
use Che\EventBand\Adapter\Amqp\Driver\DriverException;
use Che\EventBand\Adapter\Amqp\Driver\MessageDelivery;
use Che\EventBand\Adapter\Amqp\Driver\MessagePublication;

/**
 * Description of PeclAmqpDriver
 *
 * TODO: proper exceptions
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class PeclAmqpDriver implements AmqpDriver
{
    private $connection;
    private $channel;
    private $exchanges = array();
    private $queues = array();

    public function __construct(ConnectionDefinition $connectionDefinition)
    {
        $this->connection = new \AMQPConnection(array(
            'host' => $connectionDefinition->getHost(),
            'port' => $connectionDefinition->getPort(),
            'login' => $connectionDefinition->getUser(),
            'password' => $connectionDefinition->getPassword(),
            'vhost' => $connectionDefinition->getVirtualHost()
        ));
    }

    protected function getChannel()
    {
        if (!$this->channel) {
            $this->connection->connect();
            $this->channel = new \AMQPChannel($this->connection);
        }

        return $this->channel;
    }

    /**
     * @param $name
     * @return \AMQPExchange
     */
    protected function getExchange($name)
    {
        if (!isset($this->exchanges[$name])) {
            $exchange = new \AMQPExchange($this->getChannel());
            $exchange->setName($name);
            $this->exchanges[$name] = $exchange;
        }

        return $this->exchanges[$name];
    }

    /**
     * @param $name
     * @return \AMQPQueue
     */
    protected function getQueue($name)
    {
        if (!isset($this->queues[$name])) {
            $queue = new \AMQPQueue($this->getChannel());
            $queue->setName($name);
            $this->queues[$name] = $queue;
        }

        return $this->queues[$name];
    }

    public function publish(MessagePublication $publication, $exchange)
    {
        $flags = AMQP_NOPARAM;
        if ($publication->isImmediate()) {
            $flags |= AMQP_IMMEDIATE;
        }
        if ($publication->isMandatory()) {
            $flags |= AMQP_MANDATORY;
        }

        $this->getExchange($exchange)->publish(
            $publication->getMessage()->getBody(),
            $publication->getRoutingKey(),
            $flags,
            $publication->getMessage()->getProperties()
        );
    }

    public function get($queue)
    {
        $envelope = $this->getQueue($queue)->get();
        if (!$envelope) {
            return null;
        }

        return $this->createDelivery($envelope, $queue);
    }

    public function consume($queue, \Closure $callback, $timeout)
    {
        $oldTimeout = $this->connection->getTimeout();
        $this->connection->setTimeout($timeout);

        try {
            $this->getQueue($queue)->consume(function (\AMQPEnvelope $envelope) use ($callback, $queue, $oldTimeout) {
                $result = $callback(self::createDelivery($envelope, $queue));

                return $result;
            });
            $this->connection->setTimeout($oldTimeout);
        } catch (\AMQPConnectionException $e) {
            $this->connection->setTimeout($oldTimeout);

            $expectedErrors = array('interrupted system call', 'resource temporarily unavailable');
            foreach ($expectedErrors as $expectedError) {
                if (stripos($e->getMessage(), $expectedError) !== false) {
                    return;
                }
            }

            throw new DriverException('Unexpected error while consuming', $e);
        }
    }

    public function ack(MessageDelivery $delivery)
    {
        $this->getQueue($delivery->getQueue())->ack($delivery->getTag());
    }

    public function reject(MessageDelivery $delivery)
    {
        $this->getQueue($delivery->getQueue())->nack($delivery->getTag(), AMQP_REQUEUE);
    }

    protected static function createDelivery(\AMQPEnvelope $envelope, $queue)
    {
        return new MessageDelivery(
            new AmqpMessage($envelope->getBody()), // TODO: properties
            $envelope->getDeliveryTag(),
            $envelope->getExchangeName(),
            $queue,
            $envelope->getRoutingKey(),
            $envelope->isRedelivery()
        );
    }
}
