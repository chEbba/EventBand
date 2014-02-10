<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Processor;

use EventBand\BandDispatcherFactory;
use EventBand\Transport\EventConsumer;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Process consumed events through dispatcher
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class DispatchProcessor
{
    private $dispatcherFactory;
    private $consumer;

    /**
     * @param BandDispatcherFactory $dispatcherFactory
     * @param EventConsumer         $consumer
     */
    public function __construct(BandDispatcherFactory $dispatcherFactory, EventConsumer $consumer)
    {
        $this->dispatcherFactory = $dispatcherFactory;
        $this->consumer = $consumer;
    }

    /**
     * Process events
     */
    public function process($band, $timeout = 0)
    {
        if (($timeout = (int) $timeout) < 0) {
            throw new \InvalidArgumentException(sprintf('Timeout %d < 0', $timeout));
        }

        $processing = true;

        $defaultDispatcher = $this->dispatcherFactory->getDefaultDispatcher();
        $defaultDispatcher->dispatch(ProcessStartEvent::NAME, new ProcessStartEvent());

        $dispatcher = $this->dispatcherFactory->getBandDispatcher($band);
        $dispatchCallback = $this->getDispatchCallback($dispatcher, $processing);

        while ($processing) {
            $this->consumer->consumeEvents($dispatchCallback, $timeout);
            if ($processing) { // We stopped by timeout
                $dispatchTimeout = new ProcessTimeoutEvent($timeout);
                $dispatcher->dispatch(ProcessTimeoutEvent::NAME, $dispatchTimeout);

                $processing = $dispatchTimeout->isProcessing();
            }
        }

        $dispatcher->dispatch(ProcessStopEvent::NAME, new ProcessStopEvent());
    }

    private function getDispatchCallback(EventDispatcherInterface $dispatcher, &$processing)
    {
        return function ($eventName, Event $event) use ($dispatcher, &$processing) {
            $defaultDispatcher = $this->dispatcherFactory->getDefaultDispatcher();

            $dispatchStart = new DispatchStartEvent($eventName, $event);
            $defaultDispatcher->dispatch(DispatchStartEvent::NAME, $dispatchStart);

            $dispatcher->dispatch($eventName, $event);

            $dispatchStop = new DispatchStopEvent($eventName, $event);
            $defaultDispatcher->dispatch(DispatchStopEvent::NAME, $dispatchStop);
            $processing = $dispatchStop->isProcessing();

            return $processing;
        };
    }
}
