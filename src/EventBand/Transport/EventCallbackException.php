<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Transport;
use EventBand\Event;

/**
 * Exception while executing event callback on read
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class EventCallbackException extends \RuntimeException
{
    private $name;
    private $event;
    private $callback;

    /**
     * @param callable   $callback
     * @param string     $name
     * @param Event      $event
     * @param \Exception $previous
     */
    public function __construct($name, Event $event, callable $callback, \Exception $previous)
    {
        $this->name = $name;
        $this->event = $event;
        $this->callback = $callback;

        parent::__construct(
            sprintf('Exception while executing "%s" event callback "%s"', $name, $this->getCallbackAsString()),
            0,
            $previous
        );
    }

    /**
     * Get event
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get event object
     *
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Get event callback
     *
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Get string representation of callback
     *
     * @return string
     */
    public function getCallbackAsString()
    {
        if (is_string($this->callback)) {
            return $this->callback;
        }

        if (is_array($this->callback) && count($this->callback) == 2) {
            list($object, $method) = array_values($this->callback);
            is_object($object) ? sprintf('object[%s]#%s', get_class($object), spl_object_hash($object)) : strval($object);

            return sprintf('[%s, %s]', $object, $method);
        }

        if (is_object($this->callback)) {
            return sprintf('object[%s]', get_class($this->callback), spl_object_hash($this->callback));
        }

        return strval($this->callback);
    }
}
