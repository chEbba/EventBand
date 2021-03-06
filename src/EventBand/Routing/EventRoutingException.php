<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Routing;

use EventBand\Event;

/**
 * Exception on event routing
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class EventRoutingException extends \RuntimeException
{
    private $event;

    public function __construct(Event $event, $reason, \Exception $previous = null)
    {
        $this->event = $event;

        parent::__construct(sprintf('Can not route event "%s": %s', $event->getName(), $reason), 0, $previous);
    }

    public function getEvent()
    {
        return $this->event;
    }
}
