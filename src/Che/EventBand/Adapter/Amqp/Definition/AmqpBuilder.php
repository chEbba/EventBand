<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Adapter\Amqp\Definition;

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

    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param string $name
     * @param string $type
     *
     * @return ExchangeBuilder
     */
    public function exchange($name, $type = ExchangeDefinition::TYPE_DIRECT)
    {
        return $this->exchanges[$name] = new ExchangeBuilder($this, $name, $type);
    }

    public function directExchange($name)
    {
        return $this->exchange($name, ExchangeDefinition::TYPE_DIRECT);
    }

    public function fanoutExchange($name)
    {
        return $this->exchange($name, ExchangeDefinition::TYPE_FANOUT);
    }

    public function headerExchange($name)
    {
        return $this->exchange($name, ExchangeDefinition::TYPE_HEADER);
    }

    public function topicExchange($name)
    {
        return $this->exchange($name, ExchangeDefinition::TYPE_TOPIC);
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
