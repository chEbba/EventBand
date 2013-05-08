<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand;

use EventBand\Processor\DispatchStopEvent;
use EventBand\Processor\ProcessStartEvent;

/**
 * Description of EventLimiter
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class EventLimiter
{
    private $events;
    private $limit;

    public function __construct($limit)
    {
        if (($limit = (int) $limit) < 1) {
            throw new \InvalidArgumentException(sprintf('Limit %d < 1', $limit));
        }
        $this->limit = $limit;

        $this->events = 0;
    }

    public function initCounter()
    {
        $this->events = 0;
    }

    public function checkLimit(DispatchStopEvent $event)
    {
        $this->events++;

        if ($this->events >= $this->limit) {
            $event->stopDispatching();
        }
    }
}
