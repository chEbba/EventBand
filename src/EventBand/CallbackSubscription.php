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
class CallbackSubscription extends AbstractSubscription
{
    private $callback;

    public function __construct($eventName, callable $callback, $band = null)
    {
        $this->callback = $callback;

        parent::__construct($eventName, $band);
    }

    /**
     * {@inheritDoc}
     */
    public function dispatch(Event $event, BandDispatcher $dispatcher)
    {
        return call_user_func($this->callback, $event, $dispatcher) !== false;
    }
}
