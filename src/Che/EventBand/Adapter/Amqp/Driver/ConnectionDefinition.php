<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Adapter\Amqp\Driver;

/**
 * Description of ConnectionOptions
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class ConnectionDefinition
{
    private $host;
    private $port;
    private $user;
    private $password;
    private $virtualHost;

    public function __construct($host = 'localhost', $user = 'guest', $password = 'guest', $virtualHost = '/', $port = '5672')
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->port = $port;
        $this->virtualHost = $virtualHost;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getVirtualHost()
    {
        return $this->virtualHost;
    }
}
