<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Adapter\AmqpLib;

use Che\EventBand\EventConsumer;
use Che\EventBand\EventReader;
use Che\EventBand\ReadEventException;
use Che\EventBand\Serializer\EventSerializer;
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

        call_user_func($callback, $this->getSerializer()->deserializeEvent($message->body));
        $channel->basic_ack($message->delivery_info['delivery_tag']);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function consumeEvents($callback, $timeout)
    {
        try {
            $serializer = $this->getSerializer();
            $channel = $this->getChannel();

            $channel->basic_consume(
                $this->queue,
                '',
                false, false, true, false,
                function(AMQPMessage $message) use ($channel, $serializer, $callback) {
                    $event = $serializer->deserializeEvent($message->body);
                    $result = call_user_func($callback, $event);
                    $channel->basic_ack($message->delivery_info['delivery_tag']);
                    // Stop consuming
                    if (!$result) {
                        $channel->basic_cancel($message->delivery_info['consumer_tag']);
                    }
                }
            );

            while (count($channel->callbacks)) {
                $read   = array($this->getConnection()->getSocket());
                $write  = array();
                $except = array();
                if (false === ($num_changed_streams = @stream_select($read, $write, $except, $timeout))) {
                    // Check if we got interruption from system call, ex. on signal
                    $lastError = error_get_last();
                    if ($lastError && $lastError['message']) {
                        if (stripos($lastError['message'], 'interrupted system call') !== false){
                            @trigger_error(''); // clear message
                            continue;
                        } else {
                            throw new ReadEventException(
                                'Error while waiting on stream',
                                new \ErrorException($lastError['message'], 0, $lastError['type'], $lastError['file'], $lastError['line'])
                            );
                        }
                    }
                } elseif ($num_changed_streams > 0) {
                    $channel->wait();
                } else {
                    break;
                }
            }
        } catch (\Exception $e) {
            throw new ReadEventException('Exception while consuming', $e); // TODO: Throw another exception
        }
    }
}
