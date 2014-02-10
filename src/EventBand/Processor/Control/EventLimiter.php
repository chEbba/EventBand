<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Processor\Control;

use EventBand\Processor\DispatchStopEvent;
use EventBand\Processor\ProcessStartEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Description of EventLimiter
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class EventLimiter implements EventSubscriberInterface
{
    private $events;
    private $limit;

    public function __construct($limit)
    {
        if (($limit = (int) $limit) < 1) {
            throw new \InvalidArgumentException(sprintf('Limit %d < 1', $limit));
        }
        $this->limit = $limit;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ['event' => ProcessStartEvent::NAME, 'method' => 'initCounter'],
            ['event' => DispatchStopEvent::NAME, 'method' => 'checkLimit']
        ];
    }

    public function initCounter()
    {
        $this->events = 0;
    }

    public function checkLimit(DispatchStopEvent $event)
    {
        if ($this->events === null) {
            throw new \BadMethodCallException('Counter was not initialized. Use initCounter');
        }

        $this->events++;

        if ($this->events >= $this->limit) {
            $event->stopProcessing();
        }
    }
}
