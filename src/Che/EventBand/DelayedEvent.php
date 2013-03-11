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
 * Delayed event wraps original event which should be processed later
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class DelayedEvent extends Event
{
    private $originalEvent;
    private $delay;
    private $time;

    public function __construct(Event $originalEvent, $delay, \DateTime $time = null)
    {
        $this->originalEvent = $originalEvent;
        $this->delay = (int) $delay;
        if ($this->delay < 1) {
            throw new \InvalidArgumentException(sprintf('Wrong delay "%s". A positive integer expected', $delay));
        }
        $this->time = $time ?: new \DateTime();
    }

    public function getOriginalEvent()
    {
        return $this->originalEvent;
    }

    public function getDelay()
    {
        return $this->delay;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function getReadyTime()
    {
        $time = clone $this->time;
        $time->modify(sprintf('+%d seconds', $this->delay));

        return $time;
    }

    public function isReady(\DateTime $time = null)
    {
        $timestamp = $time ? $time->getTimestamp() : time();

        return $this->time->getTimestamp() + $this->delay >= $timestamp;
    }
}
