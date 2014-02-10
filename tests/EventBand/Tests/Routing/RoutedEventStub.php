<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Tests\Routing;

use Symfony\Component\EventDispatcher\Event;

/**
 * Movk class for event
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class RoutedEventStub extends Event
{
    private $property;

    public function getSelf()
    {
        return $this;
    }

    /**
     * @param mixed $property
     *
     * @return $this Provides the fluent interface
     */
    public function setProperty($property)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProperty()
    {
        return $this->property;
    }
}
