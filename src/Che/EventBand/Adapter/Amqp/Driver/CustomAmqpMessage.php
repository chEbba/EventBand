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
 * Description of CustomAmqpMessage
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class CustomAmqpMessage implements AmqpMessage
{
    private static $PROPERTY_NAMES = array(
        'body', 'headers', 'contentType', 'contentEncoding',
        'messageId', 'appId', 'userId', 'priority',
        'timestamp', 'expiration', 'type', 'replyTo'
    );

    private $body;
    private $headers;
    private $contentType;
    private $contentEncoding;
    private $messageId;
    private $appId;
    private $userId;
    private $priority;
    private $timestamp;
    private $expiration;
    private $type;
    private $replyTo;

    public function __construct($body = null)
    {
        $this->setBody($body);
        $this->headers = array();
    }

    public static function fromProperties(array $properties)
    {
        $copy = new static();
        foreach (self::getPropertyNames() as $name) {
            if (isset($properties[$name])) {
                $copy->{'set'.ucfirst($name)}($properties[$name]);
            }
        }

        return $copy;
    }

    public static function createCopy(AmqpMessage $message)
    {
        $properties = array();

        foreach (self::getPropertyNames() as $name) {
            $value = $message->{'get'.ucfirst($name)};
            if ($value !== null) {
                $properties[$name] = $value;
            }
        }

        return static::fromProperties($properties);
    }

    final protected static function getPropertyNames()
    {
        return self::$PROPERTY_NAMES;
    }

    public function setBody($body)
    {
        $this->body = (string) $body;

        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setHeader($key, $value)
    {
        $this->headers[$key] = (string) $value;

        return $this;
    }

    public function setHeaders(array $headers)
    {
        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }

        return $this;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setContentType($contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    public function getContentType()
    {
        return $this->contentType;
    }

    public function setContentEncoding($contentEncoding)
    {
        $this->contentEncoding = $contentEncoding;

        return $this;
    }

    public function getContentEncoding()
    {
        return $this->contentEncoding;
    }

    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;

        return $this;
    }

    public function getMessageId()
    {
        return $this->messageId;
    }

    public function setAppId($appId)
    {
        $this->appId = $appId;

        return $this;
    }

    public function getAppId()
    {
        return $this->appId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setPriority($priority)
    {
        $this->priority = (int) $priority;

        return $this;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setTimestamp(\DateTime $timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function setExpiration(\DateTime $expiration)
    {
        $this->expiration = $expiration;

        return $this;
    }

    public function getExpiration()
    {
        return $this->expiration;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setReplyTo($replyTo)
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    public function getReplyTo()
    {
        return $this->replyTo;
    }
}
