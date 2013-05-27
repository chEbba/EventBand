<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Transport\Amqp;

use EventBand\Transport\Amqp\Definition\AmqpDefinition;
use EventBand\Transport\Amqp\Driver\AmqpDriver;
use EventBand\Transport\Amqp\Driver\DriverException;
        use EventBand\Transport\ConfiguratorException;
use EventBand\Transport\TransportConfigurator;
use EventBand\Transport\UnsupportedDefinitionException;

/**
 * Class AmqpConfigurator
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class AmqpConfigurator implements TransportConfigurator
{
    private $driver;

    public function __construct(AmqpDriver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsDefinition($definition)
    {
        return $definition instanceof AmqpDefinition;
    }

    /**
     * {@inheritDoc}
     */
    public function setUpDefinition($definition)
    {
        if (!$this->supportsDefinition($definition)) {
            throw new UnsupportedDefinitionException($definition, 'Definition is not an AmqpDefinition');
        }

        /** @var $definition AmqpDefinition */

        try {
            foreach ($definition->getExchanges() as $exchange) {
                $this->driver->declareExchange($exchange);
            }
            foreach ($definition->getExchanges() as $exchange) {
                foreach ($exchange->getBindings() as $source => $routingKeys) {
                    foreach ($routingKeys as $routingKey) {
                        $this->driver->bindExchange($source, $exchange->getName(), $routingKey);
                    }
                }
            }

            foreach ($definition->getQueues() as $queue) {
                $this->driver->declareQueue($queue);
                foreach ($queue->getBindings() as $exchange => $routingKeys) {
                    foreach ($routingKeys as $routingKey) {
                        $this->driver->bindQueue($exchange, $queue->getName(), $routingKey);
                    }
                }
            }
        } catch (DriverException $e) {
            throw new ConfiguratorException('Driver error on declare', 0, $e);
        }
    }
}