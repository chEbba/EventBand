<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Adapter\Symfony\Command;

use EventBand\Transport\TransportConfigurator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractSetupCommand
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
abstract class AbstractSetupCommand extends Command
{
    /**
     * @return TransportConfigurator
     */
    abstract protected function getConfigurator();

    abstract protected function getConfigurationDefinition(array $options);

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = array_merge($input->getOptions(), $input->getArguments());

        $definition = $this->getConfigurationDefinition($options);

        $this->getConfigurator()->setUpDefinition($definition);
    }
}