<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Adapter\Amqp;

use Che\EventBand\Adapter\Amqp\Converter\DeliveryConversionException;
use Che\EventBand\Adapter\Amqp\Converter\MessageEventConverter;
use Che\EventBand\Adapter\Amqp\Driver\AmqpDriver;
use Che\EventBand\Adapter\Amqp\Driver\MessageDelivery;
use Che\EventBand\Reader\EventCallbackException;
use Che\EventBand\Reader\EventConsumer;
use Che\EventBand\Reader\ReadEventException;

/**
 * Description of AmqpConsumer
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class AmqpConsumer implements EventConsumer
{
    private $driver;
    private $converter;
    private $queue;

    public function __construct(AmqpDriver $driver, MessageEventConverter $converter, $queue)
    {
        $this->driver = $driver;
        $this->converter = $converter;
        $this->queue = $queue;
    }

    /**
     * {@inheritDoc}
     */
    public function readEvent($callback)
    {
        $delivery = $this->driver->get($this->queue);
        if (!$delivery) {
            return false;
        }

        $this->processDelivery($delivery, $callback);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function consumeEvents($callback, $timeout)
    {
        $this->driver->consume(
            $this->queue,
            function (MessageDelivery $delivery) use ($callback) {
                return $this->processDelivery($delivery, $callback);
            },
            $timeout
        );
    }

    public function processDelivery(MessageDelivery $delivery, $callback)
    {
        $converter = $this->converter;
        $driver = $this->driver;

        try {
            $event = $converter->createDeliveryEvent($delivery);
        } catch (DeliveryConversionException $e) {
            $driver->reject($delivery);

            throw new ReadEventException('Error on event conversion', $e);
        }

        try {
            $result = call_user_func($callback, $event);
        } catch (\Exception $e) {
            $driver->reject($delivery);

            throw new EventCallbackException($callback, $event, $e);
        }

        $driver->ack($delivery);

        return $result;
    }
}
