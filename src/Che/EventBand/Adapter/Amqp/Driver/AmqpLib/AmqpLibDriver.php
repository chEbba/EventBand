<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Adapter\Amqp\Driver\AmqpLib;

use Che\EventBand\Adapter\Amqp\Driver\AmqpDriver;
use Che\EventBand\Adapter\Amqp\Driver\AmqpMessage;
use Che\EventBand\Adapter\Amqp\Driver\DriverException;
use Che\EventBand\Adapter\Amqp\Driver\MessageDelivery;
use Che\EventBand\Adapter\Amqp\Driver\MessagePublication;
use PhpAmqpLib\Message\AMQPMessage as AmqpLibMessage;

/**
 * Description of AmqpLibDriver
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class AmqpLibDriver implements AmqpDriver
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

    public function __construct(AmqpConnectionFactory $connectionFactory)
    {
        $this->connectionFactory = $connectionFactory;
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
            $this->channel = $this->getConnection()->channel();
        }

        return $this->channel;
    }

    public function publish(MessagePublication $publication, $exchange)
    {
        $msg = $publication->getMessage();
        try {
            $this->getChannel()->basic_publish(
                new AmqpLibMessage($msg->getBody(), $msg->getProperties()),
                $exchange,
                $publication->getRoutingKey(),
                $publication->isMandatory(),
                $publication->isImmediate()
            );
        } catch (\Exception $e) {
            throw new DriverException('Basic publish error', $e);
        }
    }

    public function get($queue)
    {
        try {
            /** @var $msg AmqpLibMessage */
            $msg = $this->getChannel()->basic_get($queue, false);
        } catch (\Exception $e) {
            throw new DriverException('Basic get error', $e);
        }

        if (!$msg) {
            return null;
        }

        return $this->createDelivery($msg, $queue);
    }

    public function consume($queue, \Closure $callback, $timeout)
    {
        try {
            $channel = $this->getChannel();
            $channel->basic_consume(
                $queue,
                '',
                false, false, false, false,
                function(AMQPLibMessage $msg) use ($callback, $channel, $queue) {
                    if (!$callback($this->createDelivery($msg, $queue))) {// Stop consuming
                        $channel->basic_cancel($msg->delivery_info['consumer_tag']);
                    }
                }
            );

            while (count($channel->callbacks)) {
                $read   = array($this->getConnection()->getSocket());
                $write  = array();
                $except = array();
                if (false === ($num_changed_streams = @stream_select($read, $write, $except, $timeout))) {
                    $error = error_get_last();

                    if ($error && $error['message']) {
                        // Check if we got interruption from system call, ex. on signal
                        if (stripos($error['message'], 'interrupted system call') !== false) {
                            @trigger_error(''); // clear message
                            break;
                        }

                        throw new \RuntimeException(
                            'Error while waiting on stream',
                            new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line'])
                        );
                    }
                } elseif ($num_changed_streams > 0) {
                    $channel->wait();
                } else {
                    break;
                }
            }
        } catch (\Exception $e) {
            throw new DriverException('Basic consume error', $e);
        }
    }

    public function ack(MessageDelivery $delivery)
    {
        try {
            $this->getChannel()->basic_ack($delivery->getTag());
        } catch (\Exception $e) {
            throw new DriverException('Basic ack error', $e);
        }
    }

    public function reject(MessageDelivery $delivery)
    {
        try {
            $this->getChannel()->basic_reject($delivery->getTag(), true);
        } catch (\Exception $e) {
            throw new DriverException('Basic reject error', $e);
        }
    }

    protected function closeChannel()
    {
        if (!$this->channel) {
            return false;
        }

        $this->channel->close();
        $this->channel = null;

        return true;
    }

    protected static function createDelivery(AmqpLibMessage $msg, $queue)
    {
        $propertyNames = array(
            'content_type',
            "content_encoding",
            "application_headers",
            "delivery_mode",
            "priority",
            "correlation_id",
            "reply_to",
            "expiration",
            "message_id",
            "timestamp",
            "type",
            "user_id",
            "app_id",
            "cluster_id"
        );
        $properties = array();
        foreach ($propertyNames as $name) {
            if ($msg->has($name)) {
                $properties[$name] = $msg->get($name);
            }
        }

        return new MessageDelivery(
            new AmqpMessage($msg->body, $properties),
            $msg->delivery_info['delivery_tag'],
            $msg->delivery_info['exchange'],
            $queue,
            $msg->delivery_info['routing_key'],
            $msg->delivery_info['redelivered']
        );
    }

    protected function checkStreamError(array $error)
    {
        // Check if cleared error
        if (!$error['message']) {
            return;
        }


    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->closeChannel();
    }
}
