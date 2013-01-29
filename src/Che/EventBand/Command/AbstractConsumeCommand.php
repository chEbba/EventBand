<?php

namespace Che\EventBand\Command;

use Che\EventBand\EventConsumer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Description of AbstractConsumeCommand
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
abstract class AbstractConsumeCommand extends AbstractProcessCommand
{
    /**
     * @return EventConsumer
     */
    abstract protected function getConsumer();

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('consume');
    }

    protected function doExecute($callback, InputInterface $input, OutputInterface $output)
    {
        while(true) {
            $this->getConsumer()->consumeEvents($callback, $this->getInputTimeout($input));
            if (!$this->checkState()) {
                break;
            }
        }
    }

    protected function getEventCallback($name)
    {
        $callback = parent::getEventCallback($name);
        $self = $this;

        return function (Event $event) use ($callback, $self) {
            $callback($event);

            return $self->checkState();
        };
    }
}
