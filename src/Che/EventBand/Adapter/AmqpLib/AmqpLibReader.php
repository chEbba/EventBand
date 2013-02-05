<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Adapter\AmqpLib;

use Che\EventBand\EventCallbackException;
use Che\EventBand\EventConsumer;
use Che\EventBand\EventReader;
use Che\EventBand\ReadEventException;
use Che\EventBand\Serializer\EventSerializer;
use Che\EventBand\Serializer\SerializerException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Event reader and consumer implementation based on php-amqplib
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class AmqpLibReader extends AmqpLibAdapter implements EventReader, EventConsumer
{
    private $queue;
    private $messageCallback;

    /**
     * Constructor
     *
     * @param AmqpConnectionFactory $connectionFactory Connection factory instance
     * @param EventSerializer       $serializer        Event serializer instance
     * @param string                $queue             Name of queue to read messages.
     *                                                 Queue will not be created or bound.
     *                                                 You should setup queue by yourself
     * @param int                   $channelId         Channel id
     *
     * @see AmqpLibAdapter::__construct()
     */
    public function __construct(AmqpConnectionFactory $connectionFactory, EventSerializer $serializer, $queue,
                                $channelId = null)
    {
        $this->queue =  $queue;

        parent::__construct($connectionFactory, $serializer, $channelId);
    }

    /**
     * {@inheritDoc}
     */
    public function readEvent($callback)
    {
        $channel = $this->getChannel();

        /** @var $message AMQPMessage */
        $message = $channel->basic_get($this->queue, false);
        if (!$message) {
            return false;
        }

        $messageCallback = $this->getMessageCallback($callback);
        $messageCallback($channel, $message);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function consumeEvents($callback, $timeout)
    {
        try {
            $this->doConsume($this->getMessageCallback($callback), $timeout);
        } catch (ReadEventException $e) {
            $this->closeChannel();

            throw $e;
        } catch (\Exception $e) {
            $this->closeChannel();

            throw new ReadEventException('Exception while consuming', $e);
        }
    }

    protected function doConsume(\Closure $callback, $timeout)
    {
        $channel = $this->getChannel();
        $channel->basic_consume(
            $this->queue,
            '',
            false, false, false, false,
            function(AMQPMessage $message) use ($callback) {
                /** @var $channel \PhpAmqpLib\Channel\AMQPChannel */
                $channel = $message->delivery_info['channel'];
                if (!$callback($channel, $message)) {// Stop consuming
                    $channel->basic_cancel($message->delivery_info['consumer_tag']);
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
                    $this->checkStreamError($error);
                    continue;
                }
            } elseif ($num_changed_streams > 0) {
                $channel->wait();
            } else {
                break;
            }
        }
    }

    /**
     * @param callable $callback
     *
     * @return \Closure
     * @throws ReadEventException
     * @throws EventCallbackException
     */
    private function getMessageCallback($callback)
    {
        if (!$this->messageCallback) {
            $serializer = $this->getSerializer();
            $this->messageCallback = function (AMQPChannel $channel, AMQPMessage $msg) use ($callback, $serializer) {
                try {
                    $event = $serializer->deserializeEvent($msg->body);
                } catch (SerializerException $e) {
                    $channel->basic_reject($msg->delivery_info['delivery_tag'], true);

                    throw new ReadEventException('Error on event deserialization', $e);
                }

                try {
                    $result = call_user_func($callback, $event);
                } catch (\Exception $e) {
                    $channel->basic_reject($msg->delivery_info['delivery_tag'], true);

                    throw new EventCallbackException($callback, $event, $e);
                }

                $channel->basic_ack($msg->delivery_info['delivery_tag']);

                return $result;
            };
        }

        return $this->messageCallback;
    }

    private function checkStreamError($error)
    {
        if (stripos($error['message'], 'interrupted system call') !== false){
            @trigger_error(''); // clear message
            return;
        } else {
            throw new ReadEventException(
                'Error while waiting on stream',
                new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line'])
            );
        }
    }
}
