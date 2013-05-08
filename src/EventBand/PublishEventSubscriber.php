<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand;

use EventBand\Transport\EventPublisher;

/**
 * Class PublishEventSubscriber
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class PublishEventSubscriber
{
    private $publisher;
    private $propagation;

    /**
     * Constructor
     *
     * @param EventPublisher $publisher   Event publisher instance
     * @param bool           $propagation If false event propagation will be stopped after publish
     */
    public function __construct(EventPublisher $publisher, $propagation = true)
    {
        $this->publisher = $publisher;
        $this->propagation = (bool) $propagation;
    }

    public function __invoke(Event $event)
    {
        $this->publisher->publishEvent($event);

        return $this->propagation;
    }
}