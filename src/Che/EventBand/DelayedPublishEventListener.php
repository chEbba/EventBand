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
 * Republish original event when it is ready
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class DelayedPublishEventListener extends PublishEventListener
{
    /**
     * Publish event
     * If event is delayed method will block executing while event is not ready
     *
     * @param Event $event
     */
    public function __invoke(Event $event)
    {
        if ($event instanceof DelayedEvent) {
            $timeToPublish = $event->getReadyTime()->getTimestamp() - time();
            if ($timeToPublish > 0) {
                sleep($timeToPublish);
            }

            $event = $event->getOriginalEvent();
        }

        parent::__invoke($event);
    }
}
