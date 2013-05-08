<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Transport\Amqp\Driver;

use EventBand\Event;

/**
 * Description of EventConversionException
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class EventConversionException extends \RuntimeException
{
    private $event;

    /**
     * @param Event           $event
     * @param string          $reason
     * @param \Exception|null $previous
     */
    public function __construct(Event $event, $reason, \Exception $previous = null)
    {
        $this->event = $event;

        parent::__construct(sprintf('Can not convert event "%s": %s', $event->getName(), $reason), 0, $previous);
    }

    public function getEvent()
    {
        return $this->event;
    }
}
