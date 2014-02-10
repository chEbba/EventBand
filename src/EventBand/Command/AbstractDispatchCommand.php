<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Adapter\Symfony\Command;

use Che\ConsoleSignals\SignaledCommand;
use EventBand\BandDispatcher;
use EventBand\CallbackSubscription;
use EventBand\Processor\DispatchProcessor;
use EventBand\Processor\DispatchStopEvent;
use EventBand\Processor\ProcessTimeoutEvent;
use EventBand\Processor\StoppableProcess;
use EventBand\Transport\EventConsumer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractDispatchCommand
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
abstract class AbstractDispatchCommand extends SignaledCommand
{
    /**
     * @return BandDispatcher
     */
    abstract protected function getDispatcher();

    /**
     * @param string $band
     *
     * @return EventConsumer
     * @throws \OutOfBoundsException
     */
    abstract protected function getConsumer($band);

    protected function getBandName()
    {
        return null;
    }

    protected function getDefaultTimeout()
    {
        return 60;
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->addOption('timeout', 't', InputOption::VALUE_REQUIRED, 'Timeout to wait when no events exist', $this->getDefaultTimeout())
            ->addOption('events', null, InputOption::VALUE_REQUIRED, 'Limit of events to be dispatched', 0)
            ->addOption('time', null, InputOption::VALUE_REQUIRED, 'Time limit for dispatching', 0)
        ;
        if (!$this->getBandName()) {
            $this->addArgument('band', InputArgument::REQUIRED, 'Name of band');
        }
    }

    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $band = $this->getBandName() ?: $input->getArgument('band');
        $timeout = $input->getOption('timeout');

        $processor = new DispatchProcessor($this->getDispatcher(), $this->getConsumer($band), $band, $timeout);
        $this->configureControl($input);

        $processor->process();
    }

    protected function configureControl(InputInterface $input)
    {
        $dispatcher = $this->getDispatcher();

         $events = $input->getOption('events');
         $time = $input->getOption('time');
        // TODO: configure limiters

        $signalCallback = function (StoppableProcess $event) {
            if (!$this->isActive()) {
                $event->stopProcessing();
            }
        };

        $dispatcher->subscribe(new CallbackSubscription(DispatchStopEvent::name(), $signalCallback));
        $dispatcher->subscribe(new CallbackSubscription(ProcessTimeoutEvent::name(), $signalCallback));
    }
}
