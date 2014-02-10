<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Tests\Serializer;

use Symfony\Component\EventDispatcher\Event;

/**
 * Description of EventStub
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class NativeSerializableEventStub extends Event implements \Serializable
{
    private $error;

    public function __construct($error = null)
    {
        $this->error = $error;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        if ($this->error === 'exception') {
            throw new \Exception('Unsupported');
        }

        if ($this->error === 'error') {
            return;
        }

        return serialize([$this->error]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        if ($this->error === 'exception') {
            throw new \Exception('Unsupported');
        }

        $this->error = unserialize($serialized)[0];
    }
}
