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
     *
     * @param callback $callback Callback which should be executed on event.
     *                           Signature: void function(Event $event)
     *
     * @return bool True if event was read, false if no event exists
     * @throws ReadEventException
     */
    public function readEvent($callback);
}
