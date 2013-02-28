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
 * TODO: strict properties
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class AmqpMessage 
{
    private $body;
    private $properties;

    public function __construct($body, array $properties = array())
    {
        $this->body = $body;
        $this->properties = $properties;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getProperties()
    {
        return $this->properties;
    }
}
