<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Adapter\AmqpLib;

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
    private $host = 'localhost';
    private $port = '5672';
    private $user = 'guest';
    private $password = 'guest';
    private $virtualHost = '/';

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
     * Set host
     *
     * @param string $host
     *
     * @return $this Provides the fluent interface
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Set port
     *
     * @param string $port
     *
     * @return $this Provides the fluent interface
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Set auth user
     *
     * @param string $user
     *
     * @return $this Provides the fluent interface
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Set user password
     *
     * @param string $password
     *
     * @return $this Provides the fluent interface
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set virtual host
     *
     * @param string $virtualHost
     *
     * @return $this Provides the fluent interface
     */
    public function setVirtualHost($virtualHost)
    {
        $this->virtualHost = $virtualHost;

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
     * @throws InvalidParameterException
     */
    private function createConnection()
    {
        $requiredParams = array('host', 'port', 'user', 'password', 'virtualHost');
        foreach ($requiredParams as $param) {
            if (!isset($this->$param)) {
                throw new InvalidParameterException($param, 'Parameter is required');
            }
        }

        return new AMQPConnection($this->host, $this->port, $this->user, $this->password, $this->virtualHost);
    }
}
