<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand;

use Che\LogStock\Loader\Container\ServiceLocator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class BandDispatcherLocator
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class BandDispatcherLocator implements BandDispatcherFactory
{
    private $locator;

    public function __construct(ServiceLocator $locator)
    {
        $this->locator = $locator;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultDispatcher()
    {
        // Use empty string for a default dispatcher
        $dispatcher = $this->loadDispatcher('');
        if (!$dispatcher) {
            throw new \UnexpectedValueException('Default dispatcher with empty name not loaded from locator');
        }

        return $dispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function getBandDispatcher($band)
    {
        if (($band = (string) $band) === '') {
            throw new \InvalidArgumentException('Band should be empty');
        }

        $dispatcher = $this->loadDispatcher($band);
        if (!$dispatcher) {
            throw new \OutOfBoundsException(sprintf('No dispatcher "%s" was found'));
        }

        return $dispatcher;
    }

    /**
     * @param string $band
     *
     * @return null|object
     */
    private function loadDispatcher($band)
    {
        $dispatcher = $this->locator->getService($band);
        if (!$dispatcher instanceof EventDispatcherInterface) {
            return null;
        }

        return $dispatcher;
    }
}
