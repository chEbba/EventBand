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
    private $foo;

    public function getSelf()
    {
        return $this;
    }

    /**
     * @param mixed $foo
     *
     * @return $this Provides the fluent interface
     */
    public function setFoo($foo)
    {
        $this->foo = $foo;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFoo()
    {
        return $this->foo;
    }
}
