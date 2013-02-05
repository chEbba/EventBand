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
 * Event listener which publishes events
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class PublishEventListener
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

    public function getPropagation()
    {
        return $this->propagation;
    }

    public function getPublisher()
    {
        return $this->publisher;
    }

    public function __invoke(Event $event)
    {
        $this->publisher->publishEvent($event);
        if (!$this->propagation) {
            $event->stopPropagation();
        }
    }
}
