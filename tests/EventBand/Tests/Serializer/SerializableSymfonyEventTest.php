<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Tests\Serializer;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class SerializableSymfonyEventTest
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class SerializableSymfonyEventTest extends TestCase
{
    /**
     * @test serialize()/unserialize() uses toSerializableArray()/fromUnserializedArray()
     */
    public function serializeWithArray()
    {
        $event = new SerializableEventStub('event.name', 'data');
        $event->setDispatcher($this->getMock('Symfony\Component\EventDispatcher\EventDispatcher'));

        $serialized = serialize($event);
        $unserialized = unserialize($serialized);

        $this->assertEquals('event.name', $unserialized->getName());
        $this->assertEquals('data', $unserialized->getFoo());
        $this->assertNull($unserialized->getDispatcher());
    }
}
