<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Processor;

use EventBand\ClassNamedEvent;
use EventBand\Event;

/**
 * Description of DispatchEvent
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class DispatchStartEvent extends ClassNamedEvent
{
    private $dispatchingEvent;

    public function __construct(Event $dispatchingEvent)
    {
        $this->dispatchingEvent = $dispatchingEvent;
    }

    public function getDispatchingEvent()
    {
        return $this->dispatchingEvent;
    }
}
