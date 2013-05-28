<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Transport\Amqp\Definition;

/**
 * Class ExchangeType
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
final class ExchangeType
{
    const DIRECT  = 'direct';
    const FANOUT  = 'fanout';
    const HEADERS = 'headers';
    const TOPIC   = 'topic';

    final private function __construct() {}

    public static function getTypes()
    {
        return [
            self::DIRECT,
            self::FANOUT,
            self::HEADERS,
            self::TOPIC
        ];
    }
}