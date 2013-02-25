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
abstract class AbstractDispatchCommand extends AbstractReadCommand
{
    private $processors = array();

    abstract protected function getDispatcher();

    abstract protected function getPrefixTemplate();

    protected function getProcessor($name)
    {
        if (!isset($this->processors[$name])) {
            $this->processors[$name] = new DispatcherProcessor(
                $this->getDispatcher(),
                strtr($this->getPrefixTemplate(), array('{processor}' => $name))
            );
        }

        return $this->processors[$name];
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('dispatch');
    }
}
