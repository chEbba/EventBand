<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Transport;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event publisher. Publishes events to custom storage for later processing
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
interface EventPublisher
{
    /**
     * Publish event in container
     *
     * @param Event $event
     *
     * @throws PublishEventException
     */
    public function publishEvent(Event $event);
}
