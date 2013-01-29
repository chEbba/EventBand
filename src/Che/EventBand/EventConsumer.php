<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand;

/**
 * Event consumer. Consume events from storage.
 * Consumer implements read loop internally.
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
interface EventConsumer
{
    /**
     * Consume events and run event callback.
     * This method can be blocking but should return if no events exist in timeout seconds
     *
     * @param callback $callback Event callback. If callback returns false consumption should be stopped.
     *                           Signature: bool function(Event $event)
     * @param int      $timeout  Timeout in seconds (>= 0).
     *                           If not events occurs in $timeout seconds consumer should stop consumption.
     *
     * @throws ReadEventException
     */
    public function consumeEvents($callback, $timeout);
}
