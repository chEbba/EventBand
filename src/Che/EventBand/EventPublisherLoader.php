<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand;

use Symfony\Component\EventDispatcher\Event;

/**
 * Loader for event publishers
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
interface EventPublisherLoader
{
    /**
     * Load publisher for event
     *
     * @param Event $event
     *
     * @return EventPublisher
     * @throws LoadPublisherException
     */
    public function loadPublisherForEvent(Event $event);
}
