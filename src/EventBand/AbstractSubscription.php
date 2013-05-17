<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand;

/**
 * Class AbstractSubscription
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
abstract class AbstractSubscription implements Subscription
{
    private $eventName;
    private $band;

    public function __construct($eventName, $band = null)
    {
        $this->eventName = $eventName;
        $band = (string) $band;
        if (empty($band)) {
            $band = null;
        }
        $this->band = $band;
    }

    /**
     * {@inheritDoc}
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * {@inheritDoc}
     */
    public function getBand()
    {
        return $this->band;
    }
}