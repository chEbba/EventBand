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
 * Description of AmqpAdapter
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
interface AmqpDriver
{
    public function publish(MessagePublication $publication, $exchange);

    public function get($queue);

    public function consume($queue, \Closure $callback, $timeout);

    public function ack(MessageDelivery $delivery);

    public function reject(MessageDelivery $delivery);
}
