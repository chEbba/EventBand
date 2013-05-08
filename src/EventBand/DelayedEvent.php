<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand;

/**
 * Delayed event wraps original event which should be processed later
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
interface DelayedEvent extends Event
{
    /**
     * Get original event
     *
     * @return Event
     */
    public function getOriginalEvent();

    /**
     * Get event time
     *
     * @return mixed
     */
    public function getTime();

    /**
     * Get delay
     *
     * @return int Delay in seconds
     */
    public function getDelay();
}
