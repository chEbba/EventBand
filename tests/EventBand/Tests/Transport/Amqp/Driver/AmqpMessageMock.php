<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Tests\Transport\Amqp\Driver;

use EventBand\Transport\Amqp\Driver\AmqpMessage;

/**
 * Class AmqpMock
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class AmqpMessageMock implements AmqpMessage
{
    private $nulls;

    public function __construct($nulls = false)
    {
        $this->nulls = (bool) $nulls;
    }

    public function getBody()
    {
        return 'body';
    }

    public function getHeaders()
    {
        return ['x-header1' => 'value1', 'x-header2' => 'value2'];
    }

    public function getContentType()
    {
        return 'application/json';
    }

    public function getContentEncoding()
    {
        return 'utf-8';
    }

    public function getMessageId()
    {
        // TODO: int or string?
        return $this->nulls ? null : 666;
    }

    public function getAppId()
    {
        // TODO: int or string?
        return $this->nulls ? null : 13;
    }

    public function getUserId()
    {
        // TODO: int or string?
        return $this->nulls ? null : 123;
    }

    public function getPriority()
    {
        return 10;
    }

    public function getTimestamp()
    {
        return $this->nulls ? null : strtotime('2012-12-18 09:45:13');
    }

    public function getExpiration()
    {
        return $this->nulls ? null : 60*60*24*365*100;
    }

    public function getType()
    {
        return $this->nulls ? null : 'custom-type';
    }

    public function getReplyTo()
    {
        return $this->nulls ? null : 'reply-queue';
    }
}