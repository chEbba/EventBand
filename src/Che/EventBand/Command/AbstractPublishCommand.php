<?php

namespace Che\EventBand\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
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
        $this->setName('publish');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $event = $this->createEvent($input);
        $this->getPublisher()->publishEvent($event);

        $output->writeln(sprintf('Event was published: "%s".', $event->getName()));
    }
}
