<?php

namespace Che\EventBand\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Description of AbstractProcessCommand
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
abstract class AbstractProcessCommand extends Command
{
    private $executed = false;
    private $signalsEnabled = false;

    abstract protected function executeProcessor($processorName, InputInterface $input, OutputInterface $output);

    abstract protected function getProcessor($name);

    protected function getPredefinedProcessorName()
    {
        return null;
    }

    public function start()
    {
        $this->executed = true;
    }

    public function stop()
    {
        $this->executed = false;
    }

    public function isExecuted()
    {
        return $this->executed;
    }

    public function isActive()
    {
        if (!$this->isExecuted()) {
            return false;
        }

        if ($this->signalsEnabled) {
            pcntl_signal_dispatch();

            return $this->isExecuted();
        }

        return true;
    }

    protected function configure()
    {
        $this->addOption('no-signals', null, InputOption::VALUE_NONE, 'Disable signal handling');

        $processorName = $this->getPredefinedProcessorName();
        if (!$processorName) {
            $this->addArgument('name', InputArgument::REQUIRED, 'Name of processor');
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->start();
        $processorName = $this->getProcessorName($input);

        $output->writeln(sprintf('Started %s.', $processorName));

        if ($this->isSignalSupportEnabled($input)) {
            $this->registerSignals($output);
        }

        $this->executeProcessor($processorName, $input, $output);

        $this->stop();
        $output->writeln(sprintf('Stopped %s.', $processorName));
    }

    protected function getProcessorName(InputInterface $input)
    {
        return $this->getPredefinedProcessorName() ?: $input->getArgument('name');
    }

    protected function isSignalSupportEnabled(InputInterface $input)
    {
        return extension_loaded('pcntl') && !$input->getOption('no-signals');
    }

    protected function registerSignals(OutputInterface $output)
    {
        $callback = $this->getStopSignalCallback($output);
        pcntl_signal(SIGTERM, $callback);
        pcntl_signal(SIGINT, $callback);

        $this->signalsEnabled = true;
    }

    protected function getStopSignalCallback(OutputInterface $output)
    {
        $self = $this;// PHP 5.3 support
        return function () use ($output, $self) {
            $output->writeln('Got stop signal. Stopping...');
            $self->stop();
        };
    }
}
