<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Transport\Amqp;

use EventBand\Transport\Amqp\Driver\MessageConversionException;
use EventBand\Transport\Amqp\Driver\MessageEventConverter;
use EventBand\Transport\Amqp\Driver\AmqpDriver;
use EventBand\Transport\Amqp\Driver\DriverException;
use EventBand\Transport\Amqp\Driver\MessageDelivery;
use EventBand\Transport\EventCallbackException;
use EventBand\Transport\EventConsumer;
use EventBand\Transport\ReadEventException;

/**
 * Event consumer for AMQP Drivers
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
    public function consumeEvents(callable $callback, $timeout)
    {
        try {
            $this->driver->consume($this->queue, $this->createDeliveryCallback($callback), $timeout);
        } catch (DriverException $e) {
            throw new ReadEventException('Driver error while consuming', $e);
        }
    }

    private function createDeliveryCallback(callable $callback)
    {
        return function (MessageDelivery $delivery) use ($callback) {
            try {
                $event = $this->converter->messageToEvent($delivery->getMessage());
            } catch (MessageConversionException $e) {
                $this->driver->reject($delivery);

                throw new ReadEventException('Error on event message conversion', $e);
            }

            try {
                $result = $callback($event);
            } catch (\Exception $e) {
                $this->driver->reject($delivery);

                throw new EventCallbackException($callback, $event, $e);
            }

            $this->driver->ack($delivery);

            return $result;
        };
    }
}
