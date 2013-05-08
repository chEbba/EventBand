<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Transport\Amqp\Driver;

/**
 * Description of CustomAmqpMessage
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
final class CustomAmqpMessage implements AmqpMessage
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

    /**
     * Create message from array of properties
     *
     * @param array $properties An assoc array of [property => value]
     *
     * @return CustomAmqpMessage
     *
     * @see getPropertyNames()
     */
    public static function fromProperties(array $properties)
    {
        $copy = new self();
        foreach (self::getPropertyNames() as $name) {
            if (isset($properties[$name])) {
                $copy->{'set'.ucfirst($name)}($properties[$name]);
            }
        }

        return $copy;
    }

    public static function createCopy(AmqpMessage $message)
    {
        return static::fromProperties(self::getMessageProperties($message));
    }

    public static function getMessageProperties(AmqpMessage $message)
    {
        $properties = [];
        foreach (self::getPropertyNames() as $name) {
            $value = $message->{'get'.ucfirst($name)}();
            if ($value !== null) {
                $properties[$name] = $value;
            }
        }

        return $properties;
    }

    public static function getPropertyNames()
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
        $this->headers[strtolower($key)] = (string) $value;

        return $this;
    }

    public function setHeaders(array $headers)
    {
        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }

        return $this;
    }

    public function removeHeader($key)
    {
        unset($this->headers[strtolower($key)]);
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

    public function setTimestamp($timestamp)
    {
        $this->timestamp = (int) $timestamp;

        return $this;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function setExpiration($expiration)
    {
        $this->expiration = (int) $expiration;

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
