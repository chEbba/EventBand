<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Adapter\Amqp\Driver\AmqpLib;

use Che\EventBand\Adapter\Amqp\Driver\ConnectionDefinition;
use Che\EventBand\Adapter\InvalidParameterException;
use PhpAmqpLib\Connection\AMQPConnection;

/**
 * Lazy connection factory implementation based on builder
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class AmqpConnectionBuilder implements AmqpConnectionFactory
{
    private $connection;
    private $definition;

    public function __construct()
    {
        $this->definition = new ConnectionDefinition();
    }

    public function setDefinition(ConnectionDefinition $definition)
    {
        $this->definition = $definition;

        return $this;
    }

    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Set custom connection object
     *
     * @param AMQPConnection $connection
     *
     * @return $this Provides the fluent interface
     */
    public function setConnection(AMQPConnection $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->createConnection();
        }

        return $this->connection;
    }

    /**
     * Create connection
     *
     * @return AMQPConnection
     */
    private function createConnection()
    {
        return new AMQPConnection(
            $this->definition->getHost(),
            $this->definition->getPort(),
            $this->definition->getUser(),
            $this->definition->getPassword(),
            $this->definition->getVirtualHost()
        );
    }
}
