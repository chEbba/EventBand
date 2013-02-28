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
 * Description of AmqpMessage
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class MessagePublication
{
    private $message;
    private $routingKey;
    private $mandatory;
    private $immediate;

    public function __construct(AmqpMessage $message, $routingKey = '', $immediate = false, $mandatory = false)
    {
        $this->message = $message;
        $this->routingKey = $routingKey;
        $this->immediate = $immediate;
        $this->mandatory = $mandatory;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getRoutingKey()
    {
        return $this->routingKey;
    }

    public function isMandatory()
    {
        return $this->mandatory;
    }

    public function isImmediate()
    {
        return $this->immediate;
    }
}
