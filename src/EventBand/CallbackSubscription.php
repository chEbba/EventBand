<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand;

/**
 * Subscription dispatched with callback
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class CallbackSubscription implements Subscription
{
    private $eventName;
    private $band;
    private $callback;

    public function __construct($eventName, callable $callback, $band = '')
    {
        $this->eventName = $eventName;
        $this->callback = $callback;
        $this->band = (string) $band;
    }

    /**
     * {@inheritDoc}
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * {@inheritDoc}
     */
    public function getBand()
    {
        return $this->band;
    }

    /**
     * {@inheritDoc}
     */
    public function dispatch(Event $event, BandDispatcher $dispatcher)
    {
        return call_user_func($this->callback, $event, $dispatcher) !== false;
    }
}
