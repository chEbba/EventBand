<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand;

/**
 * Loader for processors based on a simple array
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class EventProcessorArrayLoader implements EventProcessorLoader
{
    private $processors;

    /**
     * Constructor with predefined processors
     *
     * @param array $processors An array of [name => callback]
     *
     * @see setProcessor()
     */
    public function __construct(array $processors = array())
    {
        $this->processors = array();
        foreach ($processors as $name => $callback) {
            $this->setProcessor($name, $callback);
        }
    }

    /**
     * Set event processor
     *
     * @param string   $name
     * @param callback $callback Signature: void function(Event $event)
     *
     * @return $this Provides the fluent interface
     */
    public function setProcessor($name, $callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Callback is not callable');
        }

        $this->processors[$name] = $callback;

        return $this;
    }

    /**
     * Get event processor
     *
     * @param string $name
     *
     * @return callback
     * @throws \OutOfBoundsException If no processor with $name exists
     */
    public function getProcessor($name)
    {
        if (!$this->hasProcessor($name)) {
            throw new \OutOfBoundsException(sprintf('Undefined processor "%s"', $name));
        }

        return $this->fetchProcessor($name);
    }

    /**
     * Check if processor exists
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasProcessor($name)
    {
        return isset($this->processors[$name]);
    }

    /**
     * @param string $name
     *
     * @return callback Removed callback
     * @throws \OutOfBoundsException If no processor with $name exists
     */
    public function removeProcessor($name)
    {
        if (!$this->hasProcessor($name)) {
            throw new \OutOfBoundsException(sprintf('Undefined processor "%s"', $name));
        }

        $processor = $this->fetchProcessor($name);
        $this->deleteProcessor($name);

        return $processor;
    }

    /**
     * {@inheritDoc}
     */
    public function loadProcessor($name)
    {
        try {
            return $this->getProcessor($name);
        } catch (\OutOfBoundsException $e) {
            throw new LoadProcessorException($name, 'Can not find processor', $e);
        }
    }

    /**
     * Get processor from internal storage
     *
     * @param string $name
     *
     * @return callback
     */
    protected function fetchProcessor($name)
    {
        return $this->processors[$name];
    }

    /**
     * Remove processor from internal storage
     *
     * @param string $name
     */
    public function deleteProcessor($name)
    {
        unset($this->processors[$name]);
    }
}
