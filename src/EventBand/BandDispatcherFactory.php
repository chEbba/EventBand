<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class BandDispatcherFactory
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
interface BandDispatcherFactory
{
    /**
     * @return EventDispatcherInterface
     */
    public function getDefaultDispatcher();

    /**
     * @param string $band Not empty band name
     *
     * @return EventDispatcherInterface
     */
    public function getBandDispatcher($band);
}
