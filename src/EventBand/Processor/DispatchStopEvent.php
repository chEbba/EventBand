<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Processor;

/**
 * Class DispatchStopEvent
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class DispatchStopEvent extends DispatchingEvent implements StoppableProcessEvent
{
    use StoppableProcess;

    const NAME = 'event_band.dispatch.stop';
}
