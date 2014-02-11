<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Tests;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class OpenEventDispatcher
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
abstract class ListenerContainerDispatcher implements EventDispatcherInterface
{
    public $listeners = [];

    /**
     * {@inheritDoc}
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->listeners[$eventName][$priority] = $listener;
    }

    public function getEvents()
    {
        return array_keys($this->listeners);
    }

    public function fistEventKey()
    {
        reset($this->listeners);

        return key($this->listeners);
    }
}
