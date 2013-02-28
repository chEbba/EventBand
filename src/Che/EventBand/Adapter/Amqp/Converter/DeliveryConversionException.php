<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Adapter\Amqp\Converter;

use Che\EventBand\Adapter\Amqp\Driver\MessageDelivery;

/**
 * Description of MessageConversionException
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class DeliveryConversionException extends \RuntimeException
{
    private $delivery;

    public function __construct(MessageDelivery $delivery, $reason, \Exception $previous = null)
    {
        $this->delivery = $delivery;

        parent::__construct(sprintf('Can not convert delivery "%s": %s', $delivery->getTag(), $reason), 0, $previous);
    }

    public function getDelivery()
    {
        return $this->delivery;
    }
}
