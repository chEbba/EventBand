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
interface AmqpMessage
{
    public function getBody();

    public function getHeaders();

    public function getContentType();

    public function getContentEncoding();

    public function getMessageId();

    public function getAppId();

    public function getUserId();

    public function getPriority();

    public function getTimestamp();

    public function getExpiration();

    public function getType();

    public function getReplyTo();
}
