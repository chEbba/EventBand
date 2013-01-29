<?php

namespace Che\EventBand\Command;

use Che\EventBand\EventConsumer;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Description of ReadCommand
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class ConsumeCommand extends AbstractConsumeCommand
{
    private $consumer;
    private $dispatcher;
    private $processorLoader;

    public function setConsumer(EventConsumer $reader)
    {
        $this->consumer = $reader;

        return $this;
    }

    protected function getConsumer()
    {
        if (!$this->consumer) {
            throw new \BadMethodCallException('No consumer was set');
        }

        return $this->consumer;
    }

    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->callback = null;

        return $this;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getDispatcher()
    {
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
