<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand;

/**
 * Class DelaySubscriber
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class DelaySubscriber
{
    public function __invoke(DelayedEvent $event, BandDispatcher $dispatcher)
    {
        $timeToPublish = $event->getTime()->getTimestamp() + $event->getDelay() - time();
        if ($timeToPublish > 0) {
            sleep($timeToPublish);
        }

        $dispatcher->dispatchEvent($event->getOriginalEvent());
    }
}