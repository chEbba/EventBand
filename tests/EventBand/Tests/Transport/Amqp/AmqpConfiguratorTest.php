<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Tests\Transport\Amqp;

use EventBand\Transport\Amqp\AmqpConfigurator;
use EventBand\Transport\ConfiguratorException;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Test for AmqpConfigurator
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class AmqpConfiguratorTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $driver;
    /**
     * @var AmqpConfigurator
     */
    private $configurator;

    /**
     * Setup driver mock and configurator
     */
    protected function setUp()
    {
        $this->driver = $this->getMock('EventBand\Transport\Amqp\Driver\AmqpDriver');
        $this->configurator = new AmqpConfigurator($this->driver);
    }

    /**
     * @test supportsDefinition except only AmqpDefinition
     */
    public function supportsAmqpDefinition()
    {
        $definition = $this->getMock('EventBand\Transport\Amqp\Definition\AmqpDefinition');

        $this->assertTrue($this->configurator->supportsDefinition($definition));
        $this->assertFalse($this->configurator->supportsDefinition(new \DateTime()));
    }

    /**
     * @test setUpDefinition use driver to declare queues and exchanges
     */
    public function declareQueuesAndExchanges()
    {
        $exchange1 = $this->getMock('EventBand\Transport\Amqp\Definition\ExchangeDefinition');
        $exchange2 = $this->getMock('EventBand\Transport\Amqp\Definition\ExchangeDefinition');
        $queue1 = $this->getMock('EventBand\Transport\Amqp\Definition\QueueDefinition');
        $queue2 = $this->getMock('EventBand\Transport\Amqp\Definition\QueueDefinition');

        $definition = $this->getMock('EventBand\Transport\Amqp\Definition\AmqpDefinition');
        $definition
            ->expects($this->any())
            ->method('getExchanges')
            ->will($this->returnValue([$exchange1, $exchange2]))
        ;
        $definition
            ->expects($this->any())
            ->method('getQueues')
            ->will($this->returnValue([$queue1, $queue2]))
        ;

        $this->driver
            ->expects($this->at(0))
            ->method('declareExchange')
            ->with($exchange1)
        ;
        $this->driver
            ->expects($this->at(1))
            ->method('declareExchange')
            ->with($exchange2)
        ;
        $this->driver
            ->expects($this->at(2))
            ->method('declareQueue')
            ->with($queue1)
        ;
        $this->driver
            ->expects($this->at(3))
            ->method('declareQueue')
            ->with($queue2)
        ;

        $this->configurator->setUpDefinition($definition);
    }

    /**
     * @test setUpDefinition throws exception on unsupported definition
     * @expectedException EventBand\Transport\UnsupportedDefinitionException
     */
    public function unsupportedException()
    {
        $this->configurator->setUpDefinition(new \DateTime());
    }

    /**
     * @test setUpDefinition wraps driver exception in configuration exception
     */
    public function configurationDriverException()
    {
        $exchange = $this->getMock('EventBand\Transport\Amqp\Definition\ExchangeDefinition');
        $definition = $this->getMock('EventBand\Transport\Amqp\Definition\AmqpDefinition');
        $definition
            ->expects($this->any())
            ->method('getExchanges')
            ->will($this->returnValue([$exchange]))
        ;

        $exception = $this->getMockBuilder('EventBand\Transport\Amqp\Driver\DriverException')
            ->disableOriginalConstructor()->getMock();
        $this->driver
            ->expects($this->any())
            ->method('declareExchange')
            ->will($this->throwException($exception))
        ;

        try {
            $this->configurator->setUpDefinition($definition);
        } catch (ConfiguratorException $e) {
            $this->assertSame($exception, $e->getPrevious());
            $this->assertNotInstanceOf('EventBand\Transport\UnsupportedDefinitionException', $e);
            return;
        }

        $this->fail('Exception was not thrown');
    }
}
