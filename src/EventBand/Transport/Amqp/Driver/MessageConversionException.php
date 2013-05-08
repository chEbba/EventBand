<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Transport\Amqp\Driver;

use EventBand\Transport\Amqp\Driver\AmqpMessage;

/**
 * Description of MessageConversionException
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class MessageConversionException extends \RuntimeException
{
    private $amqpMessage;

    /**
     * @param AmqpMessage     $message
     * @param string          $reason
     * @param \Exception|null $previous
     */
    public function __construct(AmqpMessage $message, $reason, \Exception $previous = null)
    {
        $this->amqpMessage = $message;

        parent::__construct(sprintf('Can not convert message: %s', $reason), 0, $previous);
    }

    public function getAmqpMessage()
    {
        return $this->amqpMessage;
    }
}
