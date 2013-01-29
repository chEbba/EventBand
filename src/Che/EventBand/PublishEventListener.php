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
    private $stopPropagation;

    /**
     * Constructor
     *
     * @param EventPublisher $publisher       Event publisher instance
     * @param bool           $stopPropagation Should listener stops event propagation after publish?
     */
    public function __construct(EventPublisher $publisher, $stopPropagation = false)
    {
        $this->publisher = $publisher;
        $this->stopPropagation = (bool) $stopPropagation;
    }

    public function __invoke(Event $event)
    {
        $this->publisher->publishEvent($event);
        if ($this->stopPropagation) {
            $event->stopPropagation();
        }
    }
}
