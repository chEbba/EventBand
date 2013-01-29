<?php

namespace Che\EventBand\Command;

use Che\EventBand\EventReader;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of EventReadCommand
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
abstract class AbstractReadCommand extends AbstractProcessCommand
{
    /**
     * Get event reader
     *
     * @return EventReader
     */
    abstract protected function getReader();

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('read');
    }

    /**
     * {@inheritDoc}
     */
    protected function doExecute($callback, InputInterface $input, OutputInterface $output)
    {
        while(true) {
            if (!$this->getReader()->readEvent($callback)) {
                sleep($this->getInputTimeout($input));
                if (!$this->checkState()) {
                    break;
                }
            }
        }
    }
}
