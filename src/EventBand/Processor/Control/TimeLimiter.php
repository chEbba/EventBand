<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Processor\Control;

use EventBand\Processor\DispatchStopEvent;
use EventBand\Processor\ProcessStartEvent;
use EventBand\Processor\ProcessTimeoutEvent;
use EventBand\Processor\StoppableProcessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class TimeLimiter
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class TimeLimiter implements EventSubscriberInterface
{
    private $startTime;
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
            ['event' => ProcessStartEvent::NAME, 'method' => 'initTimer'],
            ['event' => DispatchStopEvent::NAME, 'method' => 'checkLimit'],
            ['event' => ProcessTimeoutEvent::NAME, 'method' => 'checkLimit']
        ];
    }

    public function initTimer()
    {
        $this->startTime = time();
    }

    public function checkLimit(StoppableProcessEvent $event)
    {
        if ($this->startTime === null) {
            throw new \BadMethodCallException('Timer was not initialized. Use initTimer');
        }

        if ($this->startTime + $this->limit >= time()) {
            $event->stopProcessing();
        }
    }
}
