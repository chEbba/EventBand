<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Transport;

/**
 * Delegates configuration for internal configurators
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class DelegatingTransportConfigurator implements TransportConfigurator
{
    /**
     * @var TransportConfigurator[]
     */
    private $configurators;

    public function __construct(array $configurators = [])
    {
        foreach ($configurators as $name => $configurator) {
            $this->registerConfigurator($name, $configurator);
        }
    }

    public function registerConfigurator($name, TransportConfigurator $configurator)
    {
        $this->configurators[$name] = $configurator;
    }

    public function getConfigurators()
    {
        return $this->configurators;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsDefinition($definition)
    {
        foreach ($this->configurators as $configurator) {
            if ($configurator->supportsDefinition($definition)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function setUpDefinition($definition)
    {
        $setup = false;
        foreach ($this->configurators as $configurator) {
            if ($configurator->supportsDefinition($definition)) {
                $configurator->setUpDefinition($definition);
                $setup = true;
            }
        }

        if (!$setup) {
            throw new UnsupportedDefinitionException($definition, sprintf('No configurator found for definition'));
        }
    }
}