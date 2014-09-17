<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Transport\Amqp\Definition;

/**
 * Queue builder
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class QueueBuilder extends ModelBuilder implements QueueDefinition
{
    private $exclusive;

    private $arguments = array();

    /**
     * @return $this Provides the fluent interface
     */
    public function exclusive()
    {
        $this->exclusive = true;

        return $this;
    }

    public function isExclusive()
    {
        return $this->exclusive;
    }

    /**
     * @param array $arguments
     * @return $this
     */
    public function arguments($arguments = array())
    {
        $this->arguments = $arguments;

        return $this;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'exclusive' => $this->exclusive,
            'arguments' => $this->arguments,
        ]);
    }
}
