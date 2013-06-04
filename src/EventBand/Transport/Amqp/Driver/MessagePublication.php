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
 * Amqp message publication
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class MessagePublication implements \Serializable, \JsonSerializable
{
    private $message;
    private $persistent;
    private $mandatory;
    private $immediate;

    public function __construct(AmqpMessage $message, $persistent = true,
                                $mandatory = false, $immediate = false)
    {
        $this->message = $message;
        $this->persistent = (bool) $persistent;
        $this->mandatory = (bool) $mandatory;
        $this->immediate = (bool) $immediate;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function isPersistent()
    {
        return $this->persistent;
    }

    public function isMandatory()
    {
        return $this->mandatory;
    }

    public function isImmediate()
    {
        return $this->immediate;
    }

    /**
     * Get an array serializable with json
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'message' => CustomAmqpMessage::getMessageProperties($this->message),
            'persistent' => $this->persistent,
            'mandatory' => $this->mandatory,
            'immediate' => $this->immediate
        ];
    }

    /**
     * Serialize publication
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this->jsonSerialize());
    }

    /**
     * Unserialize array data
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        if (!is_array($data)) {
            throw new \UnexpectedValueException('Unserialized data is not an array');
        }

        // TODO: check existence
        $this->message    = CustomAmqpMessage::fromProperties($data['message']);
        $this->persistent = (bool) $data['persistent'];
        $this->mandatory  = (bool) $data['mandatory'];
        $this->immediate  = (bool) $data['immediate'];
    }
}
