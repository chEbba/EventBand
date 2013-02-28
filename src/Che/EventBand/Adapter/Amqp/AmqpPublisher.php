<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Adapter\Amqp;

use Che\EventBand\Adapter\Amqp\Converter\MessageEventConverter;
use Che\EventBand\Adapter\Amqp\Driver\AmqpDriver;
use Che\EventBand\Adapter\Amqp\Driver\DriverException;
use Che\EventBand\Publisher\EventPublisher;
use Che\EventBand\Publisher\PublishEventException;
use Symfony\Component\EventDispatcher\Event;

/**
 * Description of AmqpPublisher
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class AmqpPublisher implements EventPublisher
{
    private $driver;
    private $converter;
    private $exchange;

    public function __construct(AmqpDriver $driver, MessageEventConverter $converter, $exchange)
    {
        $this->driver = $driver;
        $this->converter = $converter;
        $this->exchange = $exchange;
    }

    /**
     * Publish event object
     *
     * @param Event $event
     *
     * @throws PublishEventException
     */
    public function publishEvent(Event $event)
    {
        try {
            $msg = $this->converter->createEventPublication($event);
            $this->driver->publish($msg, $this->exchange);
        } catch (EventConversionException $e) {
            throw new PublishEventException($event, 'Conversion error', $e);
        } catch (DriverException $e) {
            throw new PublishEventException($event, 'Message publish error', $e);
        }
    }
}
