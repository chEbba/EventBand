<?php

namespace Che\EventBand\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of AbstractPublishCommand
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
abstract class AbstractPublishCommand extends Command
{
    /**
     * @return \Che\EventBand\Publisher\EventPublisher
     */
    abstract protected function getPublisher();

    /**
     * @param InputInterface $input
     *
     * @return \Symfony\Component\EventDispatcher\Event
     */
    abstract protected function createEvent(InputInterface $input);

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('publish')
            ->addOption('count', 'c', InputOption::VALUE_REQUIRED, 'Number of events', 1);
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        for ($i = 0; $i < $input->getOption('count'); $i++) {
            $event = $this->createEvent($input);
            $this->getPublisher()->publishEvent($event);

            $output->writeln(sprintf('Event #%d was published: "%s".', $i+1, $event->getName()));
        }
    }
}
