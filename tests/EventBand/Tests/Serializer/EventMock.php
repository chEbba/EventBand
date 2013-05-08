<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Tests\Serializer;

use EventBand\Event;

/**
 * Description of EventStub
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class EventMock implements Event
{
    private $name;
    private $error;

    public function __construct($name, $error = null)
    {
        $this->name = $name;
        $this->error = $error;
    }

    public function getName()
    {
        return $this->name;
    }

    public function __sleep()
    {
        if ($this->error === 'exception') {
            throw new \Exception('Unsupported');
        }

        if ($this->error === 'error') {
            return;
        }

        return array('name');
    }

    public function __wakeup()
    {
        if ($this->error === 'exception') {
            throw new \Exception('Unsupported');
        }
    }
}
