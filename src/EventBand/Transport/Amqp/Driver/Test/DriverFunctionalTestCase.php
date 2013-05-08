<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Transport\Amqp\Driver\Test;

use EventBand\Transport\Amqp\Driver\AmqpDriver;
use EventBand\Transport\Amqp\Driver\AmqpMessage;
use EventBand\Transport\Amqp\Driver\MessageDelivery;
use EventBand\Transport\Amqp\Driver\MessagePublication;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class DriverTestCase
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
abstract class DriverFunctionalTestCase extends TestCase
{
    private $driver;

    abstract protected function createDriver();

    abstract protected function setUpAmqp();

    abstract protected function tearDownAmqp();

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->setUpAmqp();
        $this->driver = $this->createDriver();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        $this->tearDownAmqp();
    }

    /**
     * @return AmqpDriver
     */
    protected function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param string $body
     * @param array  $properties
     *
     * @return AmqpMessage|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMessage($body = '', array $properties = [])
    {
        $message = $this->getMock('EventBand\\Transport\\Amqp\\Driver\\AmqpMessage');
        $message
            ->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($body))
        ;

        foreach ($properties as $key => $value) {
            $message
                ->expects($this->any())
                ->method('get' . ucfirst($key))
                ->will($this->returnValue($value))
            ;
        }

        return $message;
    }

    /**
     * @test publish 1 message + consume 1
     */
    public function publishConsume()
    {
        $driver = $this->getDriver();
        $driver->publish(new MessagePublication($this->createMessage('body')), 'event_band.test.exchange', 'event.test');

        $executed = 0;
        $driver->consume('event_band.test.event', function (MessageDelivery $delivery) use (&$executed) {
            $executed++;
            $this->assertEquals('body', $delivery->getMessage()->getBody());
            $this->assertEquals('event_band.test.exchange', $delivery->getExchange());
            $this->assertEquals('event_band.test.event', $delivery->getQueue());
            $this->assertEquals('event.test', $delivery->getRoutingKey());
            $this->assertFalse($delivery->isRedelivered());
        }, 0);

        $this->assertEquals(1, $executed);
    }
}