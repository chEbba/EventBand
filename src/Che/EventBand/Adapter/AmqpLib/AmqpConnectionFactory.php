<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Adapter\AmqpLib;

/**
 * php-amqplib connection factory
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
interface AmqpConnectionFactory
{
    /**
     * Get active connection
     *
     * @return \PhpAmqpLib\Connection\AMQPConnection
     * @throws \RuntimeException
     */
    public function getConnection();
}
