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
 * Description of ExchangeType
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
final class ExchangeType
{
    const DIRECT = 'direct';
    const TOPIC = 'topic';
    const FANOUT = 'fanout';
    const HEADER = 'header';

    public static function getTypes()
    {
        return array(
            self::DIRECT,
            self::TOPIC,
            self::FANOUT,
            self::HEADER
        );
    }
}
