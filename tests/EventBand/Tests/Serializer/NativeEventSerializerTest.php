<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Tests\Serializer;

use EventBand\Serializer\NativeEventSerializer;
use EventBand\Serializer\SerializerException;
use EventBand\Serializer\UnexpectedResultException;
use EventBand\Serializer\UnsupportedEventException;
use EventBand\Serializer\WrongFormatException;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Test for NativeEventSerializer
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class NativeEventSerializerTest extends TestCase
{
    /**
     * @var NativeEventSerializer
     */
    private $serializer;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->serializer = new NativeEventSerializer();
    }

    /**
     * @test serializeEvent same as native serialize
     */
    public function serializeEvent()
    {
        $event = new EventMock('event.name');
        $data = $this->serializer->serializeEvent($event);
        $this->assertSame(serialize($event), $data);
    }

    /**
     * @test catch and rethrow exception in serialization
     */
    public function serializeEventException()
    {
        $event = new EventMock('event.name', 'exception');
        try {
            $this->serializer->serializeEvent($event);
        } catch (UnsupportedEventException $e) {
            return;
        }

        $this->fail('No exception');
    }

    /**
     * @test catch native errors and through exception
     */
    public function serializeEventError()
    {
        $event = new EventMock('event.name', 'error');
        try {
            $this->serializer->serializeEvent($event);
        } catch (UnsupportedEventException $e) {
            return;
        }

        $this->fail('No exception');
    }

    /**
     * @test deserializeEvent same as native deserialize
     */
    public function deserializeEvent()
    {
        $event = new EventMock('event.name');
        $this->assertEquals($event, $this->serializer->deserializeEvent(serialize($event)));
    }

    /**
     * @test exception on not an event
     */
    public function deserializeWithException()
    {
        try {
            $this->serializer->deserializeEvent(new EventMock('event.name', 'exception'));
        } catch (SerializerException $e) {
            return;
        }

        $this->fail('No exception');
    }

    /**
     * @test exception on not an event
     */
    public function deserializeWrongFormat()
    {
        try {
            $this->serializer->deserializeEvent('foo');
        } catch (WrongFormatException $e) {
            return;
        }

        $this->fail('No exception');
    }

    /**
     * @test exception on not an event
     */
    public function deserializeNotAnEvent()
    {
        try {
            $this->serializer->deserializeEvent(serialize(new \DateTime()));
        } catch (UnexpectedResultException $e) {
            return;
        }

        $this->fail('No exception');
    }
}
