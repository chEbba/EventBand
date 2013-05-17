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
 * Description of EventDispatcher
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
interface BandDispatcher
{
    /**
     * Dispatch event in band
     *
     * @param Event       $event Event for dispatching
     * @param string|null $band  Band name
     *
     * @return bool False if propagation was stopped
     */
    public function dispatchEvent(Event $event, $band = null);

    /**
     * Add subscription
     *
     * @param Subscription $subscription
     * @param int          $priority
     */
    public function subscribe(Subscription $subscription, $priority = 0);

    /**
     * @param Subscription $subscription
     */
    public function unsubscribe(Subscription $subscription);

    /**
     * Get list of subscriptions
     *
     * @return Subscription[]|\Iterator
     */
    public function getSubscriptions();

    /**
     * @param Subscription $subscription
     *
     * @return int
     * @throws \OutOfBoundsException
     */
    public function getSubscriptionPriority(Subscription $subscription);
}
