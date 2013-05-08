<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Routing;

use EventBand\Event;

/**
 * Router for events to publish them in a proper place
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
interface EventRouter
{
    /**
     * Route event
     *
     * @param Event $event
     *
     * @return string
     * @throws EventRoutingException
     */
    public function routeEvent(Event $event);
}
