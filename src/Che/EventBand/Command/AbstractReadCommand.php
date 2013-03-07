<?php

namespace Che\EventBand\Command;

use Che\EventBand\Reader\EventConsumer;
use Che\EventBand\Reader\EventReader;
use Che\EventBand\Reader\EventReaderLoader;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Description of EventReadCommand
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
abstract class AbstractReadCommand extends AbstractProcessCommand
{
    private $eventsRead = 0;
    private $eventsLimit = 0;

    /**
     * Get event reader
     *
     * @return EventReaderLoader
     */
    abstract protected function getReaderLoader();

    protected function getDefaultTimeout()
    {
        return 10;
    }

    public function isActive()
    {
        return parent::isActive() && (!$this->eventsLimit || $this->eventsRead < $this->eventsLimit);
    }


    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->addOption('no-consume', null, InputOption::VALUE_NONE, 'Force using read instead of consume')
            ->addOption('timeout', 't', InputOption::VALUE_REQUIRED, 'Timeout to wait when no events exist', $this->getDefaultTimeout())
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Limit of events to be processed', 0)
        ;
    }

    protected function getInputTimeout(InputInterface $input)
    {
        $timeout = (int) $input->getOption('timeout');
        if ($timeout < 0) {
            throw new \InvalidArgumentException('Timeout is not a non-negative integer');
        }

        return $timeout;
    }

    /**
     * {@inheritDoc}
     */
    protected function executeProcessor($processorName, InputInterface $input, OutputInterface $output)
    {
        $this->eventsRead = 0;
        $this->eventsLimit =  $input->getOption('limit');

        $reader = $this->getReaderLoader()->loadReader($processorName);
        $processor = $this->getProcessor($processorName);
        $eventsRead = &$this->eventsRead;
        $processor = function (Event $event) use ($processor, &$eventsRead) {
            call_user_func($processor, $event);
            $eventsRead++;
        };

        $timeout = $this->getInputTimeout($input);

        if ($reader instanceof EventConsumer && !$input->getOption('no-consume')) {
            $this->consumeEvents($reader, $processor, $timeout, $output);
        } else {
            $this->readEvents($reader, $processor, $timeout, $output);
        }
    }

    protected function readEvents(EventReader $reader, \Closure $processor, $timeout, OutputInterface $output)
    {
        while ($this->isActive()) {
            if (!$reader->readEvent($processor)) {
                sleep($timeout);
            }
        }
    }

    protected function consumeEvents(EventConsumer $consumer, \Closure $processor, $timeout, OutputInterface $output)
    {
        $self = $this;
        $callback = function (Event $event) use ($self, $processor) {
            $processor($event);

            return $self->isActive();
        };

        while ($this->isActive()) {
            $consumer->consumeEvents($callback, $timeout);
        }
    }
}
