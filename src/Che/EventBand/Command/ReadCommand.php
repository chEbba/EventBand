<?php

namespace Che\EventBand\Command;

use Che\EventBand\EventReader;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Description of ReadCommand
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class ReadCommand extends AbstractReadCommand
{
    private $reader;
    private $dispatcher;
    private $processorLoader;

    public function setReader(EventReader $reader)
    {
        $this->reader = $reader;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function getReader()
    {
        if (!$this->reader) {
            throw new \BadMethodCallException('No reader was set');
        }

        return $this->reader;
    }

    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getDispatcher()
    {
        if (!$this->dispatcher) {
            throw new \BadMethodCallException('No dispatcher was set');
        }

        return $this->dispatcher;
    }

    public function setProcessorLoader($processorLoader)
    {
        $this->processorLoader = $processorLoader;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getProcessorLoader()
    {
        if (!$this->processorLoader) {
            throw new \BadMethodCallException('No processorLoader was set');
        }

        return $this->processorLoader;
    }
}
