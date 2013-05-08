<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Tests\Transport\Amqp;

use EventBand\Event;
use EventBand\Transport\Amqp\AmqpConsumer;
use EventBand\Transport\Amqp\Driver\MessageConversionException;
use EventBand\Transport\Amqp\Driver\DriverException;
use EventBand\Transport\Amqp\Driver\MessageDelivery;
use EventBand\Transport\EventCallbackException;
use EventBand\Transport\ReadEventException;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Test for AmqpConsumer
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class AmqpConsumerTest extends TestCase
{
    /**
     * @var AmqpConsumer
     */
    private $consumer;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $driver;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $converter;

    /**
     * Set up consumer
     */
    protected function setUp()
    {
        $this->driver = $this->getMock('EventBand\Transport\Amqp\Driver\AmqpDriver');
        $this->converter = $this->getMock('EventBand\Transport\Amqp\Driver\MessageEventConverter');
        $this->consumer = new AmqpConsumer($this->driver, $this->converter, 'queue');
    }

    /**
     * @test consume delegates real consumption to driver
     */
    public function delegateDriverConsume()
    {
        $this->driver
            ->expects($this->once())
            ->method('consume')
            ->with('queue', $this->isType('callable'), 10)
        ;

        $this->consumer->consumeEvents(function () {}, 10);
    }

    /**
     * @test consume provide callback for driver. This callback:
     *       * converts delivery to event
     *       * call original callback with event
     *       * ack delivery
     */
    public function messageDeliveryProcessing()
    {
        $delivery = $this->createDelivery();
        $event = $this->createEvent();

        $this->converter
            ->expects($this->once())
            ->method('messageToEvent')
            ->with($delivery->getMessage())
            ->will($this->returnValue($event));
        ;

        $this->driver
            ->expects($this->once())
            ->method('consume')
            ->will($this->returnCallback(function ($queue, $callback) use ($delivery) {
                $result = call_user_func($callback, $delivery);
                $this->assertSame(true, $result);

                return $result;
            }))
        ;
        $this->driver
            ->expects($this->once())
            ->method('ack')
            ->with($delivery)
        ;

        $this->consumer->consumeEvents(function (Event $passedEvent) use ($event) {
            $this->assertSame($event, $passedEvent);

            return true;
        }, 10);
    }

    /**
     * @test consume read exception on message conversion rejects message
     */
    public function consumeMessageConversionException()
    {
        $delivery = $this->createDelivery();

        $conversionException = new MessageConversionException(
            $this->getMock('EventBand\Transport\Amqp\Driver\AmqpMessage'),
            'Conversion Error'
        );
        $this->converter
            ->expects($this->once())
            ->method('messageToEvent')
            ->will($this->throwException($conversionException));
        ;

        $this->driver
            ->expects($this->once())
            ->method('consume')
            ->will($this->returnCallback(function ($queue, $callback) use ($delivery) {
                call_user_func($callback, $delivery);
            }))
        ;
        $this->driver
            ->expects($this->once())
            ->method('reject')
            ->with($delivery)
        ;

        try {
            $this->consumer->consumeEvents(function () {}, 10);
        } catch (ReadEventException $e) {
            $this->assertSame($conversionException, $e->getPrevious());

            return;
        }

        $this->fail('Exception was not thrown');
    }

    /**
     * @test consume callback exception rejects message
     */
    public function consumeCallbackException()
    {
        $delivery = $this->createDelivery();
        $event = $this->createEvent();
        $callbackException = new \Exception('Callback error');

        $this->converter
            ->expects($this->any())
            ->method('messageToEvent')
            ->will($this->returnValue($event));
        ;

        $this->driver
            ->expects($this->once())
            ->method('consume')
            ->will($this->returnCallback(function ($queue, $callback) use ($delivery) {
                call_user_func($callback, $delivery);
            }))
        ;
        $this->driver
            ->expects($this->once())
            ->method('reject')
            ->with($delivery)
        ;

        try {
            $this->consumer->consumeEvents(function () use ($callbackException) {
                throw $callbackException;
            }, 10);
        } catch (EventCallbackException $e) {
            $this->assertSame($callbackException, $e->getPrevious());

            return;
        }

        $this->fail('Exception was not thrown');
    }

    /**
     * @test read exception on driver error
     */
    public function driverException()
    {
        $event = $this->createEvent();
        $driverException = new DriverException('Driver error');

        $this->converter
            ->expects($this->any())
            ->method('messageToEvent')
            ->will($this->returnValue($event));
        ;

        $this->driver
            ->expects($this->once())
            ->method('consume')
            ->will($this->throwException($driverException))
        ;

        try {
            $this->consumer->consumeEvents(function () {}, 10);
        } catch (ReadEventException $e) {
            $this->assertSame($driverException, $e->getPrevious());

            return;
        }

        $this->fail('Exception was not thrown');
    }

    /**
     * @return MessageDelivery|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createDelivery()
    {
        $message = $this->getMock('EventBand\Transport\Amqp\Driver\AmqpMessage');

        $delivery = $this->getMockBuilder('EventBand\Transport\Amqp\Driver\MessageDelivery')->disableOriginalConstructor()->getMock();
        $delivery
            ->expects($this->any())
            ->method('getMessage')
            ->will($this->returnValue($message));

        return $delivery;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Event
     */
    private function createEvent()
    {
        return $this->getMock('EventBand\Event');
    }
}
