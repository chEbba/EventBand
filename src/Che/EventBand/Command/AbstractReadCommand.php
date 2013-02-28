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

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->addOption('consume', 'c', InputOption::VALUE_NONE, 'Use consume instead of read')
            ->addOption('timeout', 't', InputOption::VALUE_REQUIRED, 'timeout', $this->getDefaultTimeout())
            ->addOption('events', 'e', InputOption::VALUE_REQUIRED, 'Number of events', 0)
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
        $reader = $this->getReaderLoader()->loadReader($processorName);
        $processor = $this->getProcessor($processorName);

        if ($input->getOption('consume')) {
            if (!$reader instanceof EventConsumer) {
                throw new \InvalidArgumentException(sprintf('Reader "%s<%s>" does not support consume', $processorName, get_class($reader)));
            }
            $this->consumeEvents($reader, $processor, $input, $output);
        } else {
            $this->readEvents($reader, $processor, $input, $output);
        }
    }

    protected function readEvents(EventReader $reader, $processor, InputInterface $input, OutputInterface $output)
    {
        $events = $input->getOption('events');
        $read = 0;
        while(true && (!$events || ($read < $events)) ) {
            if (!$reader->readEvent($processor)) {
                die;
                sleep($this->getInputTimeout($input));
                if (!$this->isActive()) {
                    break;
                }
            } else {
                $read++;
            }
        }
    }

    protected function consumeEvents(EventConsumer $consumer, $processor, InputInterface $input, OutputInterface $output)
    {
        $self = $this;
        $callback = function (Event $event) use ($self, $processor) {
            call_user_func($processor, $event);

            return $self->isActive();
        };

        while(true) {
            $consumer->consumeEvents($callback, $this->getInputTimeout($input));
            if (!$this->isActive()) {
                break;
            }
        }
    }
}
