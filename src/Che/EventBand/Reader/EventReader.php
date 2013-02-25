<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Reader;

/**
 * Event reader. Reads events from storage.
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
interface EventReader
{
    /**
     * Read next event and executes callback
     * This method should not be blocking.
     * If callback fails event should be requeued.
     *
     * @param callable $callback Callback which should be executed on event.
     *                           Signature: void function(Event $event)
     *
     * @return bool True if event was read, false if no event exists
     * @throws ReadEventException     On read errors
     * @throws EventCallbackException On exception in callback
     */
    public function readEvent($callback);
}
