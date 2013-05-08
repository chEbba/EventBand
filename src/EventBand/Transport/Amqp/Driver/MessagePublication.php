<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Transport\Amqp\Driver;

/**
 * Description of AmqpMessage
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class MessagePublication
{
    private $message;
    private $persistent;
    private $mandatory;
    private $immediate;

    public function __construct(AmqpMessage $message, $persistent = true,
                                $mandatory = false, $immediate = false)
    {
        $this->message = $message;
        $this->persistent = (bool) $persistent;
        $this->mandatory = (bool) $mandatory;
        $this->immediate = (bool) $immediate;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function isPersistent()
    {
        return $this->persistent;
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
