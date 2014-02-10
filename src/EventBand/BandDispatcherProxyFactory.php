<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class BandDispatcherProxyFactory
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class BandDispatcherProxyFactory implements BandDispatcherFactory
{
    const DEFAULT_BAND_PREFIX = '__event_band__';

    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Get internal dispatcher
     *
     * @return EventDispatcherInterface
     */
    public function getDefaultDispatcher()
    {
        return $this->dispatcher;
    }

    public function getBandDispatcher($band)
    {
        if (($band = (string) $band) === '') {
            throw new \InvalidArgumentException('Band should be empty');
        }

        return new BandDispatcherProxy($this->dispatcher, sprintf('%s.%s.', self::DEFAULT_BAND_PREFIX, $band));
    }
}
