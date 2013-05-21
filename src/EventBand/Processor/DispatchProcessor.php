<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Processor;

use EventBand\Event;
use EventBand\BandDispatcher;
use EventBand\Transport\EventConsumer;
use EventBand\Transport\EventReader;

/**
 * Description of EventProcessor
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class DispatchProcessor
{
    private $dispatcher;
    private $consumer;
    private $timeout;
    private $band;

    public function __construct(BandDispatcher $dispatcher, EventConsumer $consumer, $band, $timeout)
    {
        $this->dispatcher = $dispatcher;
        $this->consumer = $consumer;

        $band = (string) $band;
        if (empty($band)) {
            throw new \InvalidArgumentException('Band should not be empty');
        }
        $this->band = $band;

        if (($timeout = (int) $timeout) < 0) {
            throw new \InvalidArgumentException(sprintf('Timeout %d < 0', $timeout));
        }
        $this->timeout = $timeout;
    }

    public function process()
    {
        $dispatching = true;

        $this->dispatcher->dispatchEvent(new ProcessStartEvent());

        $dispatchCallback = $this->getDispatchCallback($this->band, $dispatching);

        while ($dispatching) {
            $this->consumer->consumeEvents($dispatchCallback, $this->timeout);
            if ($dispatching) { // We stopped by timeout
                $dispatchTimeout = new DispatchTimeoutEvent($this->timeout);
                $this->dispatcher->dispatchEvent($dispatchTimeout);

                $dispatching = $dispatchTimeout->isDispatching();
            }
        }

        $this->dispatcher->dispatchEvent(new ProcessStopEvent());
    }

    private function getDispatchCallback(&$dispatching)
    {
        return function (Event $event) use (&$dispatching) {
            $dispatchStart = new DispatchStartEvent($event);
            $this->dispatcher->dispatchEvent($dispatchStart);

            $this->dispatcher->dispatchEvent($event, $this->band);

            $dispatchStop = new DispatchStopEvent($event);
            $this->dispatcher->dispatchEvent($dispatchStop);

            return $dispatchStop->isDispatching();
        };
    }
}
