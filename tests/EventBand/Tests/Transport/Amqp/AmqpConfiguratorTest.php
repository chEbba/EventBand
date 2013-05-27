<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Tests\Transport\Amqp;

use EventBand\Transport\Amqp\AmqpConfigurator;
use EventBand\Transport\Amqp\Definition\QueueDefinition;
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
     * @test setUpDefinition use driver to declare queues and exchanges and setup bindings
     */
    public function declareQueuesAndExchanges()
    {
        $exchange1 = $this->createExchange('exchange1');
        $exchange2 = $this->createExchange('exchange2');

        ;
        $queue1 = $this->createQueue('queue1');
        $queue2 = $this->createQueue('queue2');

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

        $exchanges = [];
        $this->driver
            ->expects($this->exactly(2))
            ->method('declareExchange')
            ->with($this->callback(function ($exchange) use (&$exchanges, $exchange1, $exchange2) {
                $exchanges[] = $exchange;
                return $exchange === $exchange1 || $exchange === $exchange2;
            }))
        ;
        $queues = [];
        $this->driver
            ->expects($this->exactly(2))
            ->method('declareQueue')
            ->with($this->callback(function ($queue) use (&$queues, $queue1, $queue2) {
                $queues[] = $queue;
                return $queue === $queue1 || $queue === $queue2;
            }))
        ;

        $this->configurator->setUpDefinition($definition);
        $this->assertContains($exchange1, $exchanges);
        $this->assertContains($exchange2, $exchanges);
        $this->assertContains($queue1, $queues);
        $this->assertContains($queue2, $queues);
    }

    /**
     * @test setUpDefinition setUp exchange bindings
     */
    public function setUpExchangeBindings()
    {
        $exchange1 = $this->createExchange('exchange1', ['exchange2' => ['routing2']]);
        $exchange2 = $this->createExchange('exchange2', ['exchange1' => ['routing1']]);

        $definition = $this->getMock('EventBand\Transport\Amqp\Definition\AmqpDefinition');
        $definition
            ->expects($this->any())
            ->method('getExchanges')
            ->will($this->returnValue([$exchange1, $exchange2]))
        ;
        $definition
            ->expects($this->any())
            ->method('getQueues')
            ->will($this->returnValue([]))
        ;

        $exchanges = 0;
        $this->driver
            ->expects($this->any())
            ->method('declareExchange')
            ->will($this->returnCallback(function () use (&$exchanges) {
                $exchanges++;
            }))
        ;
        $this->driver
            ->expects($this->exactly(2))
            ->method('bindExchange')
            ->will($this->returnCallback(function () use (&$exchanges) {
                $args = func_get_args();
                $this->assertEquals(2, $exchanges);

                $this->assertTrue($args === ['exchange2', 'exchange1', 'routing2']
                        || $args === ['exchange1', 'exchange2', 'routing1']);
            }))
        ;

        $this->configurator->setUpDefinition($definition);
    }

    /**
     * @test setUpDefinition setUp queue bindings
     */
    public function setUpQueueBindings()
    {
        $queue1 = $this->createQueue('queue1', ['exchange1' => ['routing1']]);
        $queue2 = $this->createQueue('queue2', ['exchange2' => ['routing2']]);

        $definition = $this->getMock('EventBand\Transport\Amqp\Definition\AmqpDefinition');
        $definition
            ->expects($this->any())
            ->method('getExchanges')
            ->will($this->returnValue([]))
        ;
        $definition
            ->expects($this->any())
            ->method('getQueues')
            ->will($this->returnValue([$queue1, $queue2]))
        ;

        $queues = [];
        $this->driver
            ->expects($this->any())
            ->method('declareQueue')
            ->will($this->returnCallback(function (QueueDefinition $definition) use (&$queues) {
                $queues[] = $definition->getName();
            }))
        ;
        $this->driver
            ->expects($this->exactly(2))
            ->method('bindQueue')
            ->will($this->returnCallback(function () use (&$queues) {
                $args = func_get_args();
                $this->assertTrue($args === ['exchange1', 'queue1', 'routing1']
                    || $args === ['exchange2', 'queue2', 'routing2']);

                $this->assertContains($args[1], $queues);
            }))
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

    private function createExchange($name, array $bindings = [])
    {
        $exchange = $this->getMock('EventBand\Transport\Amqp\Definition\ExchangeDefinition');
        $exchange
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name))
        ;
        $exchange
            ->expects($this->any())
            ->method('getBindings')
            ->will($this->returnValue($bindings))
        ;

        return $exchange;
    }

    private function createQueue($name, array $bindings = [])
    {
        $queue = $this->getMock('EventBand\Transport\Amqp\Definition\QueueDefinition');
        $queue
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name))
        ;
        $queue
            ->expects($this->any())
            ->method('getBindings')
            ->will($this->returnValue($bindings))
        ;

        return $queue;
    }
}
