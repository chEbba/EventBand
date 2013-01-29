<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Tests;

use Che\EventBand\DelegatingEventPublisher;
use Che\EventBand\LoadPublisherException;
use Che\EventBand\PublishEventException;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\EventDispatcher\Event;

/**
 * Description of DelegatingEventPublisherTest
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class DelegatingEventPublisherTest extends TestCase
{
    /**
     * @var \Che\EventBand\EventPublisherLoader|\PHPUnit_Framework_MockObject_MockObject
     */
    private $loader;
    /**
     * @var DelegatingEventPublisher
     */
    private $publisher;

    protected function setUp()
    {
        $this->loader = $this->getMock('Che\EventBand\EventPublisherLoader');
        $this->publisher = new DelegatingEventPublisher($this->loader);
    }

    /**
     * @test publishEvent delegates publish to loaded internal publisher
     */
    public function delegate()
    {
        $event = new Event();
        /** @var $internalPublisher \Che\EventBand\EventPublisher|\PHPUnit_Framework_MockObject_MockObject */
        $internalPublisher = $this->getMock('Che\EventBand\EventPublisher');
        $internalPublisher
            ->expects($this->once())
            ->method('publishEvent')
            ->with($event);
        $this->loader
            ->expects($this->once())
            ->method('loadPublisherForEvent')
            ->with($event)
            ->will($this->returnValue($internalPublisher));

        $this->publisher->publishEvent($event);
    }

    /**
     * @test publishEvent throws exception on loading problems
     */
    public function loadException()
    {
        $event = new Event();
        $internalException = new LoadPublisherException($event, 'Load problem');
        $this->loader
            ->expects($this->once())
            ->method('loadPublisherForEvent')
            ->will($this->throwException($internalException));

        try {
            $this->publisher->publishEvent($event);
        } catch (PublishEventException $e) {
            $this->assertContains('Can not find publisher to delegate to', $e->getMessage());
            $this->assertSame($internalException, $e->getPrevious());

            return;
        }

        $this->fail('Exception was not thrown');
    }
}
