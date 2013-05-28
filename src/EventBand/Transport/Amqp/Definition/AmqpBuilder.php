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
     * @param array $options
     *
     * @return ConnectionBuilder
     */
    public function connection(array $options = [])
    {
       return $this->connection->options($options);
    }

    /**
     * @param string $name
     * @param string $type
     * @param array  $options
     *
     * @return ExchangeBuilder
     */
    public function exchange($name, $type = ExchangeType::DIRECT, array $options = [])
    {
        return $this->exchanges[$name] = (new ExchangeBuilder($this, $name, $type))->options($options);
    }

    public function directExchange($name, array $options = [])
    {
        return $this->exchange($name, ExchangeType::DIRECT, $options);
    }

    public function fanoutExchange($name, array $options = [])
    {
        return $this->exchange($name, ExchangeType::FANOUT, $options);
    }

    public function headersExchange($name, array $options = [])
    {
        return $this->exchange($name, ExchangeType::HEADERS, $options);
    }

    public function topicExchange($name, array $options = [])
    {
        return $this->exchange($name, ExchangeType::TOPIC, $options);
    }

    public function getExchanges()
    {
        return $this->exchanges;
    }

    /**
     * @param string $name
     * @param array $options
     *
     * @return QueueBuilder
     */
    public function queue($name, array $options = [])
    {
        return $this->queues[$name] = (new QueueBuilder($this, $name))->options($options);
    }

    public function getQueues()
    {
        return $this->queues;
    }
}
