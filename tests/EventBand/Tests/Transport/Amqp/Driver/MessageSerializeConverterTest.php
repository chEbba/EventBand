<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Tests\Transport\Amqp\Driver;

use EventBand\Transport\Amqp\Driver\MessageSerializeConverter;
use EventBand\Transport\Amqp\Driver\CustomAmqpMessage;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class MessagePrototypeSerializeConverterTest
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class MessageSerializeConverterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $serializer;
    /**
     * @var MessageSerializeConverter
     */
    private $converter;

    protected function setUp()
    {
        $this->serializer = $this->getMock('EventBand\Serializer\EventSerializer');
        $this->converter = new MessageSerializeConverter($this->serializer);
    }

    /**
     * @test event conversion will serialize event to message body
     */
    public function eventSerialization()
    {
        $event = $this->getMock('EventBand\Event');
        $this->serializer
            ->expects($this->once())
            ->method('serializeEvent')
            ->with($event)
            ->will($this->returnValue('serialized string'))
        ;

        $message = $this->converter->eventToMessage($event);
        $this->assertInstanceOf('EventBand\Transport\Amqp\Driver\AmqpMessage', $message);
        $this->assertEquals('serialized string', $message->getBody());
    }

    /**
     * @test message conversion will deserialize event from message body
     */
    public function messageBodyDeserialization()
    {
        $message = $this->getMock('EventBand\Transport\Amqp\Driver\AmqpMessage');
        $message
            ->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue('serialized string'))
        ;

        $event = $this->getMock('EventBand\Event');
        $this->serializer
            ->expects($this->once())
            ->method('deserializeEvent')
            ->with('serialized string')
            ->will($this->returnValue($event))
        ;

        $converted = $this->converter->messageToEvent($message);
        $this->assertSame($event, $converted);
    }

    /**
     * @test eventToMessage get properties from prototype
     */
    public function prototypeProperties()
    {
        $prototype = new AmqpMessageMock();
        $converter = new MessageSerializeConverter($this->serializer, $prototype);
        $event = $this->getMock('EventBand\Event');

        $this->serializer
            ->expects($this->once())
            ->method('serializeEvent')
            ->will($this->returnValue('serialized string'))
        ;

        $message = $converter->eventToMessage($event);
        $this->assertInstanceOf('EventBand\Transport\Amqp\Driver\AmqpMessage', $message);

        $messageProperties = CustomAmqpMessage::getMessageProperties($message);
        $expectedProperties = CustomAmqpMessage::getMessageProperties($prototype);
        $expectedProperties['body'] = 'serialized string';

        $this->assertEquals($expectedProperties, $messageProperties);
    }
}
