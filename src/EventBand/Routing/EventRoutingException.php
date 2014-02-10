<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Routing;

use Symfony\Component\EventDispatcher\Event;

/**
 * Exception on event routing
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class EventRoutingException extends \RuntimeException
{
    private $name;
    private $event;

    /**
     * @param string          $name
     * @param Event           $event
     * @param string          $reason
     * @param \Exception|null $previous
     */
    public function __construct($name, Event $event, $reason, \Exception $previous = null)
    {
        $this->event = $event;

        parent::__construct(sprintf('Can not route event "%s": %s', $name, $reason), 0, $previous);
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getEvent()
    {
        return $this->event;
    }
}
