<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Transport\Amqp;

use EventBand\Logger\PublicationLogger;
use EventBand\Transport\Amqp\Driver\EventConversionException;
use EventBand\Transport\Amqp\Driver\MessageEventConverter;
use EventBand\Transport\Amqp\Driver\AmqpDriver;
use EventBand\Transport\Amqp\Driver\DriverException;
use EventBand\Transport\Amqp\Driver\MessagePublication;
use EventBand\Transport\EventPublisher;
use EventBand\Transport\PublishEventException;
use EventBand\Routing\EventRouter;
use EventBand\Event;

/**
 * Event publisher for AMQP drivers
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class AmqpPublisher implements EventPublisher
{

    /**
     * @var Driver\AmqpDriver
     */
    private $driver;

    /**
     * @var Driver\MessageEventConverter
     */
    private $converter;

    /**
     * @var string
     */
    private $exchange;

    /**
     * @var \EventBand\Routing\EventRouter
     */
    private $router;

    /**
     * @var bool
     */
    private $persistent;

    /**
     * @var bool
     */
    private $mandatory;

    /**
     * @var bool
     */
    private $immediate;

    /**
     * @var PublicationLogger
     */
    protected $logger;


    public function __construct(AmqpDriver $driver, MessageEventConverter $converter, $exchange,
                                EventRouter $router = null, $persistent = true,
                                $mandatory = false, $immediate = false, PublicationLogger $logger = null)
    {
        $this->driver = $driver;
        $this->converter = $converter;
        $this->exchange = $exchange;
        $this->router = $router;
        $this->persistent = $persistent;
        $this->mandatory = $mandatory;
        $this->immediate = $immediate;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function publishEvent(Event $event)
    {
        try {
            $publication = new MessagePublication(
                $this->converter->eventToMessage($event),
                $this->persistent,
                $this->mandatory,
                $this->immediate
            );

            $this->driver->publish($publication, $this->exchange, $routingKey = $this->getEventRoutingKey($event));

            if ($this->logger !== null) {
                $this->logger->published($publication, $this->exchange, $routingKey);
            }

        } catch (EventConversionException $e) {
            throw new PublishEventException($event, 'Event to message conversion error', $e);
        } catch (DriverException $e) {
            throw new PublishEventException($event, 'Message publish error', $e);
        }
    }

    private function getEventRoutingKey(Event $event)
    {
        return $this->router ? $this->router->routeEvent($event) : '';
    }
}
