<?php

namespace Che\EventBand\Command;

use Che\EventBand\DispatcherProcessor;
use Che\EventBand\Reader\EventReaderLoader;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Description of ReadCommand
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class DispatchCommand extends AbstractDispatchCommand
{
    private $readerLoader;
    private $dispatcher;
    private $prefixTemplate = '';

    public function setReaderLoader(EventReaderLoader $reader)
    {
        $this->readerLoader = $reader;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getReaderLoader()
    {
        if (!$this->readerLoader) {
            throw new \BadMethodCallException('No reader was set');
        }

        return $this->readerLoader;
    }

    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }

    public function getDispatcher()
    {
        if (!$this->dispatcher) {
            throw new \BadMethodCallException('No dispatcher was set');
        }

        return $this->dispatcher;
    }

    public function setPrefixTemplate($prefixTemplate)
    {
        $this->prefixTemplate = $prefixTemplate;
        return $this;
    }

    public function getPrefixTemplate()
    {
        return $this->prefixTemplate;
    }
}
