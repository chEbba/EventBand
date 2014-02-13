<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Transport;

/**
 * Extended event reader which can consume events from storage
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
interface EventConsumer
{
    /**
     * Consume events and run event callback.
     * This method can be blocking but should return if no events exist in timeout seconds.
     * If callback fails event should be requeued.
     *
     * @param callable $callback Event callback. Signature: bool function(Event $event)
     *                           If callback returns false consumption should be stopped.
     * @param int      $timeout  Timeout in seconds (>= 0).
     *                           If not events occurs in $timeout seconds consumer should stop consumption.
     *
     * @throws ReadEventException     On errors while event consumption
     * @throws EventCallbackException On exception in callback
     */
    public function consumeEvents(callable $callback, $timeout);
}
