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
 * Description of ConnectionBuilder
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class ConnectionBuilder implements ConnectionDefinition
{
    private $builder;
    private $host = 'localhost';
    private $port = '5672';
    private $user = 'guest';
    private $password = 'guest';
    private $virtualHost = '/';

    public function __construct(AmqpBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function end()
    {
        return $this->builder;
    }

    public function options(array $options)
    {
        foreach ($options as $key => $value) {
            $this->$key($value);
        }

        return $this;
    }

    public function host($host)
    {
        $this->host = $host;

        return $this;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function port($port)
    {
        $this->port = $port;

        return $this;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function user($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function password($password)
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function virtualHost($virtualHost)
    {
        $this->virtualHost = $virtualHost;

        return $this;
    }

    public function getVirtualHost()
    {
        return $this->virtualHost;
    }
}
