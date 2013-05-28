<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Transport\Amqp\Definition;

/**
 * Description of AMqpBuilder
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class AmqpBuilder implements AmqpDefinition
{
    private $connection;
    private $queues = array();
    private $exchanges = array();

    public function __construct()
    {
        $this->connection = new ConnectionBuilder($this);
    }

    /**
     * @return ConnectionBuilder
     */
    public function connection()
    {
       return $this->connection;
    }

    /**
     * @param string $name
     * @param string $type
     *
     * @return ExchangeBuilder
     */
    public function exchange($name, $type = ExchangeType::DIRECT)
    {
        return $this->exchanges[$name] = new ExchangeBuilder($this, $name, $type);
    }

    public function directExchange($name)
    {
        return $this->exchange($name, ExchangeType::DIRECT);
    }

    public function fanoutExchange($name)
    {
        return $this->exchange($name, ExchangeType::FANOUT);
    }

    public function headersExchange($name)
    {
        return $this->exchange($name, ExchangeType::HEADERS);
    }

    public function topicExchange($name)
    {
        return $this->exchange($name, ExchangeType::TOPIC);
    }

    public function getExchanges()
    {
        return $this->exchanges;
    }

    /**
     * @param string $name
     *
     * @return QueueBuilder
     */
    public function queue($name)
    {
        return $this->queues[$name] = new QueueBuilder($this, $name);
    }

    public function getQueues()
    {
        return $this->queues;
    }
}
