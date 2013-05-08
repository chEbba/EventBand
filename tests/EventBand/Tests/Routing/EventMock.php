<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Tests\Routing;

use EventBand\Event;

/**
 * Movk class for event
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class EventMock implements Event
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'mock';
    }

    public function getTime()
    {
        return new \DateTime();
    }
}
