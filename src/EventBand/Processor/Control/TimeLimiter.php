<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand;

use EventBand\Processor\DispatchStopEvent;

/**
 * Class TimeLimiter
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class TimeLimiter
{
    private $startTime;
    private $limit;

    public function __construct($limit)
    {
        if (($limit = (int) $limit) < 1) {
            throw new \InvalidArgumentException(sprintf('Limit %d < 1', $limit));
        }
        $this->limit = $limit;

        $this->startTime = 0;
    }

    public function initTimer()
    {
        $this->startTime = time();
    }

    public function checkLimit(DispatchStopEvent $event)
    {
        if ($this->startTime + $this->limit >= time()) {
            $event->stopDispatching();
        }
    }
}