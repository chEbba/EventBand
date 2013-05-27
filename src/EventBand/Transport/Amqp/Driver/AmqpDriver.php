<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Transport\Amqp\Driver;

use EventBand\Transport\Amqp\Definition\ExchangeDefinition;
use EventBand\Transport\Amqp\Definition\QueueDefinition;

/**
 * Description of AmqpAdapter
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
interface AmqpDriver
{
    public function publish(MessagePublication $publication, $exchange, $routingKey = '');

    public function consume($queue, callable $callback, $timeout);

    public function ack(MessageDelivery $delivery);

    public function reject(MessageDelivery $delivery);

    public function declareExchange(ExchangeDefinition $exchange);

    public function bindExchange($target, $source, $routingKey = '');

    public function declareQueue(QueueDefinition $queue);

    public function bindQueue($queue, $exchange, $routingKey = '');
}
